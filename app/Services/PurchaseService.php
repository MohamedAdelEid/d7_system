<?php

namespace App\Services;

use App\Traits\Stock;
use App\Models\Branch;
use App\Models\Contact;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Activity_log;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;
use App\Models\ProductBranchDetails;
use Illuminate\Support\Facades\Hash;
use App\Models\PurchaseUpdateHistory;
use App\Services\ActivityLogsService;

class PurchaseService
{
    use Stock;
    public $StockService;
    public $TransactionService;
    public $PaymentTransactionService;
    public $activityLogsService;
    public function __construct(
        StockService $StockService,
        TransactionService $TransactionService,
        PaymentTransactionService $PaymentTransactionService,
        ActivityLogsService $activityLogsService
    ) {
        $this->StockService = $StockService;
        $this->TransactionService = $TransactionService;
        $this->PaymentTransactionService = $PaymentTransactionService;
        $this->activityLogsService = $activityLogsService;
    }

    public function open_stock($purchase_lines_array, $branch_id)
    {
        $data = [
            'branch_id'  => $branch_id,
            'type'  => "opening_stock",
            'status'  => "final",
            'transaction_date'  => now(),
        ];
        $transaction = $this->TransactionService->CreateTransaction($data);

        $transaction = $this->TransactionService->CreatePurchaseLines($transaction, $data, $purchase_lines_array);

        $this->StockService->bulckAddToStockByPurchaseLines($transaction, $transaction->TransactionPurchaseLines);
        $description = 'تم فتح مخزون جديد. ' . PHP_EOL;
        $description .= 'رقم المعاملة: ' . $transaction->id . PHP_EOL;
        $branch = Branch::find($branch_id);
        $description .= 'اسم الفرع: ' . $branch->name . PHP_EOL;

        foreach ($purchase_lines_array as $purchase_line) {
            $product = Product::findOrFail($purchase_line['product_id']);
            $description .= 'المنتج: ' . $product->name . ', الكمية: ' . $purchase_line['quantity'] . ', السعر: ' . ($purchase_line['unit_price'] ?? 'N/A')  . PHP_EOL;
        }

        $this->activityLogsService->insert([
            'subject'     => $transaction,
            'title'       => 'فتح مخزون',
            'description' => $description,
            'proccess_type' => 'products',
            'user_id'     => auth()->id(),
        ]);
    }

    public function CreatePurchase($data, $purchase_lines_array, $request)
    {

        $account_id = $data["account_id"];
        unset($data["account_id"]);
        $data["type"] = 'purchase';
        $data["transaction_date"] = now();
        isset($data['status']) ? $input["status"] = $data['status'] : null;
        $discount_value = $data['discount_value'] ?? 0;
        $discount_type = $data['discount_type'] ?? null;
        $delivery_status = $data['delivery_status'] ?? "ordered";
        $data['payment_status'] = $data['payment_type'] == 'cash' ? 'final' : 'due';
        $balance_contact = Contact::find($data['contact_id'])->balance;
        $transaction = $this->TransactionService->CreateTransaction($data);
       
        $transaction = $this->TransactionService->CreatePurchaseLines($transaction, $data, $purchase_lines_array);
  
        $purchase_total = $this->getPurchaseTotal($transaction);
        $final_total = $purchase_total;
   
        // Handle discount
        if ($discount_type == 'percentage') {
            $discount_amount = ($purchase_total * $discount_value) / 100;
            $discount_value = $discount_amount;
        } elseif ($discount_type == 'fixed_price') {
            $discount_amount = $discount_value;
        } else {
            $discount_amount = 0;
        }
        $final_total -= $discount_amount;
        $transaction->update(['total' => $purchase_total, 'discount_value' => $discount_value, 'discount_type' => $discount_type, 'final_price' => $final_total]);
        // dd($request);

        if ($input["status"] == "final" && $transaction->delivery_status == 'delivered')
        {
            $this->StockService->bulckAddToStockByPurchaseLines($transaction, $transaction->TransactionPurchaseLines);
        }
           


      
          
        if ($request->purchase_type == 'cash') {
            // dd($data);
            $payment_data = [
                'transaction_id' => $transaction->id,
                'contact_id'     => $data['contact_id'] ?? null,
                'account_id'     => $account_id,
                'amount'         => $final_total,
                'method'         => 'cash',
                'operation'  => 'add',
                'contact_balace_no_effect' => true,
                'type'           => 'purchase',
                'created_by'      => auth()->id(),
            ];
            $transaction->update(['payment_status' => 'final']);
            $this->PaymentTransactionService->Create($payment_data);
        } else if ($request->purchase_type == 'credit' && $balance_contact > 0) {

           

            $payments = Payment::with('PaymentTransaction')
                ->withSum('PaymentTransaction', 'amount')
                ->where('contact_id', $data["contact_id"])
                ->get();

            $totalPaymentRemainder = 0;
            foreach ($payments as $payment) {

                $payment_transaction_sum_amount = $payment->payment_transaction_sum_amount;
                $remainder = $payment->amount - $payment_transaction_sum_amount;
              
                $totalPaymentRemainder += $remainder;
                if ($remainder == 0) {
                    continue;
                }
                // dd($payment);
                $total = $transaction->final_price;
                if ($transaction->payment_status != "due") {
                    $total = $transaction->final_price - $transaction->load('PaymentsTransaction')->PaymentsTransaction->sum('amount');
                }


                if ($remainder >= $total) {
                    $PaymentTransaction = PaymentTransaction::create([
                        'payment_id' => $payment->id,
                        'transaction_id' => $transaction->id,
                        'contact_id'     => $data['contact_id'],
                        'account_id'     =>  $account_id,
                        'amount'         =>  $total,
                        'method'         => 'credit',
                    ]);

                    $transaction->update(['payment_status' => 'final']);

                    $this->PaymentTransactionService->ContactSubtract($transaction->Contact, $total);
                    break;
                } else {

                    $PaymentTransaction = PaymentTransaction::create([
                        'payment_id' => $payment->id,
                        'transaction_id' => $transaction->id,
                        'contact_id'     => $data['contact_id'],
                        'account_id'     =>  $account_id,
                        'amount'         =>  $remainder,
                        'method'         => 'credit',
                    ]);

                    $transaction->update(['payment_status' => 'partial']);

                    $this->PaymentTransactionService->ContactAdd($transaction->Contact, $remainder);
                }
            }

            if ($transaction->payment_status == "partial") {
                $remainderPayment = $transaction->total - $transaction->load('PaymentsTransaction')->PaymentsTransaction->sum('amount');
                $this->PaymentTransactionService->ContactAdd($transaction->Contact, $remainderPayment);
            }

            // if no has payment and still $balance is greater than 0 
            $transactionOpeningBalance = Transaction::where('contact_id', $data['contact_id'])
                ->where('type', 'opening_balance')
                ->first();

            $paymentForOpeningBalance = Payment::where('contact_id', $data['contact_id'])
                ->where('for', 'decrement_opening_balance')->sum('amount');

            $remainderOpeningBalance = $transactionOpeningBalance->final_price - $paymentForOpeningBalance;

            if ($totalPaymentRemainder == 0 && $balance_contact > 0 && $remainderOpeningBalance > 0) {

                $amount = '';

                if ($balance_contact >= $transaction->final_price) {
                    $amount = $transaction->final_price;

                    $transaction->update(['payment_status' => 'final']);
                } else {
                    $amount = $balance_contact;
                    $transaction->update(['payment_status' => 'partial']);
                }

                $payment_data = [
                    'transaction_id' => $transaction->id,
                    'contact_id'     => $data['contact_id'] ?? null,
                    'account_id'     => $account_id,
                    'amount'         => $amount,
                    'method'         => 'credit',
                    'operation'      => 'add',
                    'contact_balace_no_effect' => true,
                    'type'           => 'purchase',
                    'for'           => 'decrement_opening_balance',
                ];

                $this->PaymentTransactionService->Create($payment_data);


                $this->PaymentTransactionService->ContactSubtract($transaction->Contact, $transaction->final_price);
            }
        } else if ($request->purchase_type == 'multi_pay') {

            $amount = $data['amount'];

            $payment_status = 'final';
            $amountDiffrence = 0;
            if ($final_total > $data['amount']) {
                $payment_status = 'partial';
                $amountDiffrence = $final_total - $data['amount'];
            }

            $payment_data = [
                'transaction_id' => $transaction->id,
                'contact_id'     => $data['contact_id'] ?? null,
                'account_id'     => $account_id,
                'amount'         => $amount,
                'method'         => 'cash',
                'operation'      => 'add',
                'contact_balace_no_effect' => true,
                'type'           => 'sell',
                'created_by'     => auth()->id(),
            ];

            $transaction->update(['payment_status' => $payment_status]);

            $this->PaymentTransactionService->Create($payment_data);

            $this->PaymentTransactionService->ContactSubtract($transaction->Contact, $amountDiffrence);
        } else {
           
            $this->PaymentTransactionService->ContactSubtract($transaction->Contact, $transaction->final_price);
            
        }   
      
        return $transaction;
    }

    public function UpdatePurchase($purchaseTransaction, $data, $purchase_lines_array)
    {
        $old_total = $this->getPurchaseTotal($purchaseTransaction);
        $old_total = $this->getPurchaseTotal($purchaseTransaction);
        $old_final_total = $purchaseTransaction->final_price;
        $changes = [];
        $old_lines = $purchaseTransaction->TransactionPurchaseLines->keyBy('product_id')->toArray();

        //update stock
        if ($data["status"] == "final" && $purchaseTransaction->delivery_status == 'delivered') {
            foreach ($purchase_lines_array as $item) {
                $product = Product::findOrFail($item['product_id']);
                $item["unit_id"] = $item['unit_id'] ?? $product->unit_id;
                $mainQuantity = $this->getMainUnitQuantityFromSubUnit($product, $item['unit_id'], $item['quantity']);
                $purchase_line = $purchaseTransaction->TransactionPurchaseLines()
                    ->where('product_id', $item['product_id'])
                    ->first();
                    
               
                if (!isset($purchase_line) || $mainQuantity != $purchase_line->main_unit_quantity) {
                    if (!isset($purchase_line) || $mainQuantity > $purchase_line->main_unit_quantity) {
                        $new_quantity = (!isset($purchase_line)) ? $mainQuantity : $mainQuantity - $purchase_line->main_unit_quantity;
                        $this->StockService->addToStock($item['product_id'], $purchaseTransaction->branch_id, $new_quantity);
                    } else {
                        $new_quantity = $purchase_line->main_unit_quantity - $mainQuantity;
                        $this->StockService->SubtractFromStock($item['product_id'], $purchaseTransaction->branch_id, $new_quantity);
                    }
                }
            }
        }
    
        // $final_total -= $discount_amount;
        // Update Purchase transaction
        $this->TransactionService->UpdateTransaction($purchaseTransaction, $data);
        $this->TransactionService->UpdateOrCreateTransactionPurchaseLines($purchaseTransaction, $purchase_lines_array);
        $purchase_total = $this->getPurchaseTotal($purchaseTransaction);
        $discount_amount = 0;
        // dd($data);
        if ($data['discount_type'] == 'percentage') {
            $discount_amount = ($purchase_total * $data['discount_value']) / 100;
            $discount_value = $discount_amount;
        } elseif ($data['discount_type'] == 'fixed_price') {
            $discount_amount = $data['discount_value'];
        } else {
            $discount_amount = 0;
        }
        
        $final_total = $purchase_total - $discount_amount;
         // Add NEW RECORD For Update History
        $new_total = $purchase_total;
        $new_final_total = $final_total;
        foreach ($purchase_lines_array as $line) {
            $product = Product::find($line['product_id']);
            $old_line = $old_lines[$line['product_id']] ?? null;
            
            if (!$old_line || 
                $old_line['quantity'] != $line['quantity'] || 
                $old_line['unit_price'] != $line['unit_price']) {
                
                $changes[] = sprintf(
                    "Product: %s - Old Qty: %s, New Qty: %s, Old Price: %s, New Price: %s",
                    $product->name,
                    $old_line['quantity'] ?? 0,
                    $line['quantity'],
                    $old_line['unit_price'] ?? 0,
                    $line['unit_price']
                );
            }
        }
    
        // Create history record
        PurchaseUpdateHistory::create([
            'transaction_id' => $purchaseTransaction->id,
            'old_total' => $old_total,
            'new_total' => $new_total,
            'old_final_price' => $old_line['quantity'] ?? 0,
            'new_final_price' => $line['quantity'],
            'changes_summary' => implode("\n", $changes),
            'updated_by' => auth()->id()
        ]);
        $purchaseTransaction->update(['total' => $purchase_total, 'final_price' => $final_total]);
      
        if ($purchaseTransaction->PaymentTransaction && $purchaseTransaction->payment_type == 'cash') {

            $this->PaymentTransactionService->Update($purchaseTransaction->PaymentTransaction, $final_total, true);
            
        } else {
            $this->PaymentTransactionService->ContactAdd($purchaseTransaction->Contact, ($old_final_total - $final_total));
        }
    }

    public function delete($transaction)
    {
        //remove payment trandaction
        if ($transaction->PaymentTransaction && $transaction->payment_status == 'final') {

            $this->PaymentTransactionService->delete($transaction->PaymentTransaction, true);
        } else {

            $this->PaymentTransactionService->ContactAdd($transaction->Contact, $transaction->final_price);
        }
        //return stock
        foreach ($transaction->TransactionPurchaseLines as $line) {
            $this->StockService->SubtractFromStock($line->product_id, $transaction->branch_id, $line->main_unit_quantity);
        }
        //delete
        $transaction->TransactionPurchaseLines()->delete();
        $transaction->delete();
    }

    public function getPurchaseTotal($purchaseTransaction)
    {
        return $purchaseTransaction->TransactionPurchaseLines()
            ->select(DB::raw('SUM(quantity * unit_price) as total'))
            ->value('total');
    }
}
