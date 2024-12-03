<?php

namespace App\Services;

use App\Traits\Stock;
use App\Models\Contact;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\Activity_log;
use App\Models\SaleUpdateHistory;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;
use App\Models\ProductBranchDetails;

class SellService
{
    use Stock;
    public $StockService;
    public $TransactionService;
    public $PaymentTransactionService;
    public $ActivityLogsService; // Added ActivityLogsService
    public function __construct(
        StockService $StockService,
        TransactionService $TransactionService,
        PaymentTransactionService $PaymentTransactionService,
        ActivityLogsService $ActivityLogsService
    ) {
        $this->StockService = $StockService;
        $this->TransactionService = $TransactionService;
        $this->PaymentTransactionService = $PaymentTransactionService;
        $this->ActivityLogsService = $ActivityLogsService;
    }
    public function product_row($product, $branch_id, $sell_line = null)
    {
        $available_quantity = 0;
        $product_branch_details = $product->ProductBranchDetails()->where('branch_id', $branch_id)->first();
        if ($product_branch_details)
            $available_quantity = $product_branch_details->qty_available;

        $data = [
            'id'    => $product->id,
            'name' => $product->name,
            'units' => $product->GetAllUnits(),
            'unit_id' => ($sell_line->unit_id) ?? null,
            'quantity'  => ($sell_line->quantity) ?? 0,
            'available_quantity' => $available_quantity,
            'unit_price'    => ($sell_line->unit_price) ?? $product->unit_price,
            'total'    => ($sell_line->final_price) ?? 0,
            'min_sale'  => $product->min_sale,
            'max_sale'  => $product->max_sale,
        ];

        return $data;
    }

    public function CreateSell($data, $sell_lines_array, $request)
    {

        $settings = Setting::first();

        // Perform validations first

        $balance_contact = Contact::find($data['contact_id'])->balance;
        $account_id = $data["account_id"];
        unset($data["account_id"]);

        $data["type"] = 'sell';
        $data["total_due_before"] = $balance_contact;
        $data["transaction_date"] = now();
        isset($data['payment_type']) ? $input["status"] = $data['status'] : null;

        $data['payment_status'] = $data['payment_type'] == 'draft'
            ? 'draft'
            : ($data['payment_type'] == 'cash' ? 'final' : 'due');




        $transaction = $this->TransactionService->CreateTransaction($data);
        $transaction = $this->TransactionService->CreateSellLines($transaction, $data, $sell_lines_array);
        $sell_total = $this->getSellTotal($transaction);
        $discount_value = $data['discount_value'] ?? 0;
        $discount_type = $data['discount_type'] ?? null;

        if ($discount_type == 'percentage') {
            $discount_amount = ($sell_total * $discount_value) / 100;
            $discount_value = $discount_amount;
        } elseif ($discount_type == 'fixed_price') {
            $discount_amount = $discount_value;
        } else {
            $discount_amount = 0;
        }

        $final_total = $sell_total - $discount_amount;

        $transaction->update([
            'total' => $sell_total,
            'discount_value' => $discount_value,
            'discount_type' => $discount_type,
            'final_price' => $final_total
        ]);

        if ($input["status"] == "final") {
            $this->StockService->bulckSubtractFromStockBySellLines($transaction, $transaction->TransactionSellLines);
        }

        if ($request->sell_type == 'cash') {
            $payment_data = [
                'transaction_id' => $transaction->id,
                'contact_id'     => $data['contact_id'] ?? null,
                'account_id'     => $account_id,
                'amount'         => $final_total,
                'method'         => 'cash',
                'operation'      => 'subtract',
                'contact_balace_no_effect' => true,
                'type'           => 'sell',
                'created_by'     => auth()->id(),
            ];

            $transaction->update(['payment_status' => 'final']);
            $this->PaymentTransactionService->Create($payment_data);
        } else if ($request->sell_type == 'credit' && $balance_contact < 0) {
            
      

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

                    $this->PaymentTransactionService->ContactAdd($transaction->Contact, $total);
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
            
            // if no has payment and still $balance is less than 0 
            $transactionOpeningBalance = Transaction::where('contact_id', $data['contact_id'])
            ->where('type','opening_balance')
            ->first();

         
            if ($totalPaymentRemainder == 0 && $balance_contact < 0 && $transactionOpeningBalance->final_price < 0) {
                
                $amount = '';

                if ($balance_contact * -1 >= $transaction->final_price) {
                    $amount = $transaction->final_price;
               
                    $transaction->update(['payment_status' => 'final']);
                } else {
                    $amount = $balance_contact * -1;
                    $transaction->update(['payment_status' => 'partial']);
                }  
       
                $payment_data = [
                    'transaction_id' => $transaction->id,
                    'contact_id'     => $data['contact_id'] ?? null,
                    'account_id'     => $account_id,
                    'amount'         => $amount,
                    'method'         => 'credit',
                    'operation'      => 'subtract',
                    'contact_balace_no_effect' => true,
                    'type'           => 'sell',
                    'for'           => 'decrement_opening_balance',
                ];

                $this->PaymentTransactionService->Create($payment_data);

                // $transactionOpeningBalance->increment('final_price',$amount);
                $this->PaymentTransactionService->ContactAdd($transaction->Contact, $transaction->final_price);
                
            }
            
        } else if ($request->sell_type == 'multi_pay') {

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
                'operation'      => 'subtract',
                'contact_balace_no_effect' => true,
                'type'           => 'sell',
                'created_by'     => auth()->id() ?? null,
            ];

            $transaction->update(['payment_status' => $payment_status]);

            $this->PaymentTransactionService->Create($payment_data);

            $this->PaymentTransactionService->ContactAdd($transaction->Contact, $amountDiffrence);
        } else {
            $this->PaymentTransactionService->ContactAdd($transaction->Contact, $transaction->final_price);
        }

        return $transaction;
    }

    public function UpdateSell($sellTransaction, $data, $sell_lines_array)
{
    $old_total = $this->getSellTotal($sellTransaction);
    $old_final_total = $sellTransaction->final_price;
    $changes = [];
    $old_lines = $sellTransaction->TransactionSellLines->keyBy('product_id')->toArray();

    // Update stock
    if ($data["status"] == "final") {
        foreach ($sell_lines_array as $item) {
            $product = Product::findOrFail($item['product_id']);
            $item["unit_id"] = $item['unit_id'] ?? $product->unit_id;
            $mainQuantity = $this->getMainUnitQuantityFromSubUnit($product, $item['unit_id'], $item['quantity']);
            $sell_line = $sellTransaction->TransactionSellLines()
                ->where('product_id', $item['product_id'])
                ->first();

            if (!isset($sell_line) || $mainQuantity != $sell_line->main_unit_quantity) {
                if (!isset($sell_line) || $mainQuantity > $sell_line->main_unit_quantity) {
                    $new_quantity = (!isset($sell_line)) ? $mainQuantity : $mainQuantity - $sell_line->main_unit_quantity;
                    $this->StockService->SubtractFromStock(
                        $item['product_id'],
                        $sellTransaction->branch_id,
                        $new_quantity
                    );
                } else {
                    $new_quantity = $sell_line->main_unit_quantity - $mainQuantity;
                    $this->StockService->addToStock(
                        $item['product_id'],
                        $sellTransaction->branch_id,
                        $new_quantity
                    );
                }
            }
        }
    }

    // Update sell transaction
    $this->TransactionService->UpdateTransaction($sellTransaction, $data);
    $this->TransactionService->UpdateOrCreateTransactionSellLines($sellTransaction, $sell_lines_array);

    // Calculate new total
    $sell_total = $this->getSellTotal($sellTransaction);

    // Apply discount
    $discount_value = $data['discount_value'] ?? 0;
    $discount_type = $data['discount_type'] ?? 'fixed_price';

    if ($discount_type === 'percentage') {
        $discount_amount = ($sell_total * $discount_value) / 100;
    } else {
        $discount_amount = $discount_value;
    }

    $new_final_total = max(0, $sell_total - $discount_amount);

    // Log changes
    foreach ($sell_lines_array as $line) {
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

    SaleUpdateHistory::create([
        'transaction_id' => $sellTransaction->id,
        'old_total' => $old_total,
        'new_total' => $sell_total,
        'old_final_price' =>  $old_line['quantity'] ?? 0,
        'new_final_price' => $line['quantity'],
        'changes_summary' => implode("\n", $changes),
        'updated_by' => auth()->id()
    ]);

    // Update transaction totals
    $sellTransaction->update([
        'total' => $sell_total,
        'final_price' => $new_final_total,
    ]);

    if ($sellTransaction->PaymentTransaction && $sellTransaction->payment_type == 'cash') {
        $this->PaymentTransactionService->Update($sellTransaction->PaymentTransaction, $new_final_total, true);
    } else {
        $this->PaymentTransactionService->ContactSubtract($sellTransaction->Contact, ($old_total - $new_final_total));
    }
}


    public function getSellTotal($transaction)
    {
        return $transaction->TransactionSellLines()
            ->select(DB::raw('SUM(quantity * unit_price) as total'))
            ->value('total');
    }

    public function delete($sell)
    {
        //remove payment trandaction
        if ($sell->PaymentTransaction) {
            $this->PaymentTransactionService->delete($sell->PaymentTransaction, true);
        } else {
            $this->PaymentTransactionService->ContactSubtract($sell->Contact, $sell->final_price);
        }

        //return stock
        foreach ($sell->TransactionSellLines as $line) {
            $this->StockService->addToStock($line->product_id, $sell->branch_id, $line->main_unit_quantity);
        }
        //delete
        $sell->TransactionSellLines()->delete();
        $sell->delete();
    }

    public function FinishSell($sellTransaction, $data, $sell_lines_array)
    {
        $settings = Setting::first();

        $balance_contact = Contact::find($sellTransaction->contact_id)->balance;
        $account_id = $data["account_id"];
        unset($data["account_id"]);

        $data["type"] = 'sell';
        $data["total_due_before"] = $balance_contact;
        $data["transaction_date"] = now();
        isset($data['payment_type']) ? $input["status"] = $data['status'] : null;

        $data['payment_status'] = $data['payment_type'] == 'draft'
            ? 'draft'
            : ($data['payment_type'] == 'cash' ? 'final' : 'due');

        $old_total = $this->getSellTotal($sellTransaction);

        //update stock
        if ($data["status"] == "final") {
            foreach ($sell_lines_array as $item) {
                $product = Product::findOrFail($item['product_id']);
                $item["unit_id"] = $item['unit_id'] ?? $product->unit_id;
                $mainQuantity = $this->getMainUnitQuantityFromSubUnit($product, $item['unit_id'], $item['quantity']);
                $sell_line = $sellTransaction->TransactionSellLines()
                    ->where('product_id', $item['product_id'])
                    ->first();
                if (!isset($sell_line) || $mainQuantity != $sell_line->main_unit_quantity) {
                    if (!isset($sell_line) || $mainQuantity > $sell_line->main_unit_quantity) {
                        $new_quantity = (!isset($sell_line)) ? $mainQuantity : $mainQuantity - $sell_line->main_unit_quantity;
                        $this->StockService->SubtractFromStock(
                            $item['product_id'],
                            $sellTransaction->branch_id,
                            $new_quantity
                        );
                    } else {
                        $new_quantity = $sell_line->main_unit_quantity - $mainQuantity;
                        $this->StockService->addToStock(
                            $item['product_id'],
                            $sellTransaction->branch_id,
                            $new_quantity
                        );
                    }
                }
            }
        }
        //update sell ransaction
        $this->TransactionService->UpdateTransaction($sellTransaction, $data);
        $this->TransactionService->UpdateOrCreateTransactionSellLines($sellTransaction, $sell_lines_array);
        $sell_total = $this->getSellTotal($sellTransaction);

        $sellTransaction->update(['total' => $sell_total, 'final_price' => $sell_total]);

        if ($sellTransaction->PaymentTransaction && $sellTransaction->payment_type == 'cash') {

            $this->PaymentTransactionService->Update($sellTransaction->PaymentTransaction, $sell_total, true);
        } else {
            $this->PaymentTransactionService->ContactSubtract($sellTransaction->Contact, ($old_total - $sell_total));
        }
    }

}
