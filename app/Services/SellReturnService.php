<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SellReturnService
{
    public $StockService;
    public $TransactionService;
    public $PaymentTransactionService;
    public function __construct(
        StockService $StockService,
        TransactionService $TransactionService,
        PaymentTransactionService $PaymentTransactionService
    ) {
        $this->StockService = $StockService;
        $this->TransactionService = $TransactionService;
        $this->PaymentTransactionService = $PaymentTransactionService;
    }
    public function create($main_sell, $data, $return_lines_array)
    {
        $data["type"] = 'sell_return';
        $data["transaction_date"] = now();
        $data["final_price"] = now();
        $data["return_transaction_id"] = $main_sell->id;
        isset($data['status']) ? $input["status"] = $data['status'] : null;


        $return_transaction = $this->TransactionService->CreateTransaction($data);

        $return_transaction = $this->TransactionService->CreateRetunLines($return_transaction, $main_sell, $data, $return_lines_array);

        $RetunTotal = $this->getRetunTotal($return_transaction);

        $return_transaction->update(['total' => $RetunTotal, "final_price" => $RetunTotal]);

        $main_sell->decrement('total', $RetunTotal);

        $main_sell->decrement('final_price', $RetunTotal);

        if ($input["status"] == "final")
            $this->StockService->bulckAddToStockByRetunLines($return_transaction, $return_transaction->TransactionPurchaseLines);



        // old way for return sell    
        // if($main_sell->payment_type == 'cash'){
        //     $payment_data = [
        //         'transaction_id' => $return_transaction->id,
        //         'contact_id'     => $main_sell->contact_id,
        //         'account_id'     => ($main_sell->PaymentTransaction->account_id)??null,
        //         'amount'         => $RetunTotal,
        //         'method'         => 'cash',
        //         'operation'         => 'add',
        //         'contact_balace_no_effect'  => true,
        //     ];
        //     $this->PaymentTransactionService->Create($payment_data);
        // } else {

        //     $this->PaymentTransactionService->ContactSubtract($return_transaction->Contact, $return_transaction->total);
        // } // end old way for return sell

        $this->PaymentTransactionService->ContactSubtract($return_transaction->Contact, $return_transaction->total);
        return true;
    }

    public function delete($sell_return)
    {
        //update stock
        $this->StockService->bulckSubtractFromStockByRetunLines($sell_return, $sell_return->TransactionPurchaseLines);

        //update payment
        // if ($sell_return->PaymentTransaction) {
        //     $contact_balace_no_effect = ($sell_return->TransactionFromReturnTransaction->payment_type == 'cash') ? true : false;
        //     $this->PaymentTransactionService->delete($sell_return->PaymentTransaction, $contact_balace_no_effect);
        // }
        // if ($sell_return->TransactionFromReturnTransaction->payment_type != 'cash') {
        //     $this->PaymentTransactionService->ContactAdd($sell_return->Contact, $sell_return->final_price);
        // }
        $this->PaymentTransactionService->ContactAdd($sell_return->Contact, $sell_return->final_price);
        //delete return transction
        $RetunTotal = $this->getRetunTotal($sell_return);

        $main_sell = $sell_return->TransactionFromReturnTransaction;
        $main_sell->update(['total' => ($main_sell->final_price + $RetunTotal), 'final_price' => ($main_sell->final_price + $RetunTotal)]);
        foreach ($sell_return->TransactionPurchaseLines as $return_line) {
            $product = Product::findOrFail($return_line['product_id']);
            $mainQuantity = $this->TransactionService->getMainUnitQuantityFromSubUnit($product, $return_line['unit_id'], $return_line['quantity']);
            $sell_line = $main_sell->TransactionSellLines()->where('id', $return_line['transactions_sell_line_id'])->first();
            if ($sell_line) {
                $sell_line->update([
                    'quantity'  => $sell_line['quantity'] + $return_line['quantity'],
                    'total' => ($sell_line['quantity'] + $return_line['quantity']) * $return_line['unit_price'],
                    'main_unit_quantity'    => $sell_line['main_unit_quantity'] + $mainQuantity,
                    'return_quantity'  => $sell_line['return_quantity'] - $return_line['quantity'],
                ]);
            }
        }
        $sell_return->TransactionPurchaseLines()->delete();
        $sell_return->delete();
    }

    public function getRetunTotal($transaction)
    {
        return $transaction->TransactionPurchaseLines()
            ->select(DB::raw('SUM(quantity * unit_price) as total'))
            ->value('total');
    }
}
