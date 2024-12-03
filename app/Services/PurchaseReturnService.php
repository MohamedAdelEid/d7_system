<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PurchaseReturnService
{
    public $StockService;
    public $TransactionService;
    public $PaymentTransactionService;
    public function __construct(StockService $StockService,
                                TransactionService $TransactionService,
                                PaymentTransactionService $PaymentTransactionService) {
        $this->StockService = $StockService;
        $this->TransactionService = $TransactionService;
        $this->PaymentTransactionService = $PaymentTransactionService;
    }
    public function create($main_purchase, $data, $return_lines_array){
        $data["type"] = 'purchase_return';
        $data["transaction_date"] = now();
        $data["payment_type"] = $main_purchase->payment_type;
        $data["return_transaction_id"] = $main_purchase->id;
        isset($data['status']) ? $input["status"] = $data['status'] : null;

        $return_transaction = $this->TransactionService->CreateTransaction($data);
        $return_transaction = $this->TransactionService->CreatePurchaseRetunLines($return_transaction, $main_purchase, $return_lines_array);
        $RetunTotal = $this->getRetunTotal($return_transaction);
        $return_transaction->update(['total' => $RetunTotal]);
        $return_transaction->update(['final_price' => $RetunTotal]);
        $main_purchase->decrement('total', $RetunTotal);
        $main_purchase->decrement('final_price', $RetunTotal);
        if($input["status"] == "final")
            $this->StockService->bulckSubtractFromStockBySellLines($return_transaction,$return_transaction->TransactionSellLines);

        if($main_purchase->payment_type == 'cash'){
           
            $payment_data = [
                'transaction_id' => $return_transaction->id,
                'contact_id'     => $main_purchase->contact_id,
                'account_id'     => ($main_purchase->PaymentTransaction->account_id)??null,
                'amount'         => $RetunTotal,
                'method'         => 'cash',
                'operation'         => 'subtract',
                'contact_balace_no_effect'  => true,
            ];
            $this->PaymentTransactionService->Create($payment_data);
        } else {
        
            $this->PaymentTransactionService->ContactAdd($return_transaction->Contact, $return_transaction->final_price);
        }

        return $return_transaction;
    }

    public function delete($purchase_return){
        //update stock
        $this->StockService->bulckAddToStockByRetunLines($purchase_return,$purchase_return->TransactionSellLines);

        //update payment
        if($purchase_return->PaymentTransaction){
            $contact_balace_no_effect = ($purchase_return->TransactionFromReturnTransaction->payment_type == 'cash') ? true : false;
            $this->PaymentTransactionService->delete($purchase_return->PaymentTransaction, $contact_balace_no_effect);
        }
        if($purchase_return->TransactionFromReturnTransaction->payment_type != 'cash'){
            $this->PaymentTransactionService->ContactSubtract($purchase_return->Contact, $purchase_return->final_price);
        }

        //delete return transction
        $RetunTotal = $this->getRetunTotal($purchase_return);
        $main_sell = $purchase_return->TransactionFromReturnTransaction;
        $main_sell->update(['final_price' => ($main_sell->final_price + $RetunTotal) , 'total' => ($main_sell->total + $RetunTotal)]);
        foreach($purchase_return->TransactionSellLines as $return_line){
            $product = Product::findOrFail($return_line['product_id']);
            $mainQuantity = $this->TransactionService->getMainUnitQuantityFromSubUnit($product, $return_line['unit_id'], $return_line['quantity']);
            $PurchaseLine = $main_sell->TransactionPurchaseLines()->where('id', $return_line['transactions_purchase_line_id'])->first();
            if($PurchaseLine){
                $PurchaseLine->update([
                    'quantity'  => $PurchaseLine['quantity'] + $return_line['quantity'],
                    // 'total' => ($PurchaseLine['quantity'] + $return_line['quantity']) * $return_line['unit_price'],
                    'main_unit_quantity'    => $PurchaseLine['main_unit_quantity'] + $mainQuantity,
                    'return_quantity'  => $PurchaseLine['return_quantity'] - $return_line['quantity'],
                ]);
            }
        }
        $purchase_return->TransactionSellLines()->delete();
        $purchase_return->delete();
    }

    public function getRetunTotal($transaction){
        return $transaction->TransactionSellLines()
                    ->select(DB::raw('SUM(quantity * unit_price) as total'))
                    ->value('total');
    }
}