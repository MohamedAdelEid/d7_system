<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Account;
use App\Models\Contact;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Services\ActivityLogsService;

class PaymentTransactionService
{

    public $activityLogsService;
    public function __construct(ActivityLogsService $activityLogsService)
    {
        $this->activityLogsService = $activityLogsService;
    }

    public function create($data)
    {

        $Payment = Payment::create([
            'contact_id'     => $data['contact_id'] ?? null,
            'account_id'     => $data['account_id'] ?? null,
            'amount'         => $data['amount'] ?? null,
            'method'         => $data['method'] ?? 'cash',
            'type'           => $data['type'] ?? null,
            'created_by'     => $data['created_by'] ?? null,
            'for'           => $data['for'] ?? null,
        ]);


        if (isset($data['transaction_id']) && $data['transaction_id']) {

            $transaction = Transaction::find($data['transaction_id']);

            if ($transaction->payment_status = "partial") {


                $amount =  $transaction->final_price - $transaction->PaymentsTransaction->sum('amount');
            } else {

                $amount = $transaction->final_price;
            }

            if ($data['amount'] >= $amount) {
                $transaction->update(['payment_status' => 'final']);
            } else {

                $transaction->update(['payment_status' => 'partial']);
            }

            $PaymentTransaction = PaymentTransaction::create([
                'payment_id' => $Payment->id,
                'transaction_id' => $data['transaction_id'] ?? null,
                'contact_id'     => $data['contact_id'] ?? null,
                'account_id'     => $data['account_id'] ?? null,
                'amount'         => $data['amount'] ?? null,
                'method'         => $data['method'] ?? 'cash',
            ]);
        } else if (isset($data['contact_id']) && $data['contact_id']) {

      
            $contact = Contact::find($data['contact_id']);
           
            $type = $contact->type == "supplier" ? "purchase" : "sell";
            $transactionNotFinal = Transaction::with('PaymentsTransaction')
                ->where(function ($q) use ($type) {
                    $q->where('type', $type)
                        ->orWhere('type', 'opening_balance');
                })
                ->where('contact_id', $data['contact_id'])

                ->where('payment_status', "!=", "final")->get();

            $totalAmountPayment = $data['amount'];

            foreach ($transactionNotFinal as $transaction) {

             
                $final_price = abs($transaction->final_price);
    
                if ($transaction->payment_status == "partial") {
                 
                    $amount =  $final_price - $transaction->PaymentsTransaction->sum('amount');
                } else {

                    $amount = $final_price;
                }
             
                if ($totalAmountPayment >= $amount) {
                    
                    $amount = 0;

                    if ($transaction->payment_status = "partial") {
                        $amount =  abs($transaction->final_price) - $transaction->PaymentsTransaction->sum('amount');
                    } else {
                        $amount = abs($transaction->final_price);
                    }

                    $transaction->update(['payment_status' => 'final']);

                    $PaymentTransaction = PaymentTransaction::create([
                        'payment_id' => $Payment->id,
                        'transaction_id' =>  $transaction->id,
                        'contact_id'     => $data['contact_id'] ?? null,
                        'account_id'     => $data['account_id'] ?? null,
                        'amount'         => $amount,
                        'method'         => $data['method'] ?? 'cash',
                    ]);



                    $totalAmountPayment =       $totalAmountPayment -  $amount;
                    
                   
                    $PaymentTransaction->update(['operation' => $data['operation']]);
                } else if ($totalAmountPayment < $amount && $totalAmountPayment != 0) {

                    $transaction->update(['payment_status' => 'partial']);

                    $PaymentTransaction = PaymentTransaction::create([


                        'payment_id' => $Payment->id,

                        'transaction_id' =>  $transaction->id,

                        'contact_id'     => $data['contact_id'] ?? null,

                        'account_id'     => $data['account_id'] ?? null,

                        'amount'         => $totalAmountPayment,

                        'method'         => $data['method'] ?? 'cash',
                    ]);

                    $PaymentTransaction->update(['operation' => $data['operation']]);

                    $totalAmountPayment = 0;
                }
                $decrement_opening_balance =  $contact->type == "supplier" ?  $transaction->final_price > 0 :  $transaction->final_price < 0;
              
                if ($transaction->type == 'opening_balance' && $decrement_opening_balance)
                {
                    $Payment->update([
                        'for' => "decrement_opening_balance"
                    ]);
                }
              
            }

            // handle The remaining  amount 

        }

        $contact = Contact::find($data['contact_id'] ?? null);
        $account = Account::find($data['account_id'] ?? null);

        if (isset($data['operation'])) {

            $Payment->update(['operation' => $data['operation']]);



            $contact_balace_no_effect = false;

            if (isset($data['contact_balace_no_effect'])) {
                $contact_balace_no_effect = $data['contact_balace_no_effect'];
            }

            if ($data['operation'] == 'add') {

                if ($contact && !$contact_balace_no_effect) {
                    $contact->increment('balance', $data['amount']);
                }


                if ($account) {
                    if ($Payment->for != "decrement_opening_balance") {
                        $account->decrement('balance', $data['amount']);
                    }
                }
            } else {

                if ($contact && !$contact_balace_no_effect) {
                    $contact->decrement('balance', $data['amount']);
                }


                if ($account) {
                    if ($Payment->for != "decrement_opening_balance") {
                        $account->increment('balance', $data['amount']);
                    }
                }
            }
        }
       
        return $Payment;
    }

    public function ContactAdd($contact, $amount)
    {
        $contact->increment('balance', $amount);
    }

    public function ContactSubtract($contact, $amount)
    {
        $contact->decrement('balance', $amount);
    }

    public function delete($paymentTransaction, $contact_balace_no_effect = false)
    {
        $contact = $paymentTransaction->contact;
        $account = $paymentTransaction->account;
        $payment = $paymentTransaction->Payment;

        if ($paymentTransaction['operation'] == 'add') {
            if ($contact && !$contact_balace_no_effect) {
                $contact->decrement('balance', $paymentTransaction['amount']);
            }

            if ($contact && $payment->for == "decrement_opening_balance") {
                $contact->decrement('balance', $paymentTransaction['amount']);
            }

            if ($payment)
                $payment->decrement('amount', $paymentTransaction['amount']);

            if ($account)
                if ($paymentTransaction->Transaction->type == "sell") {
                    if ($payment->for != "decrement_opening_balance") {
                        $account->decrement('balance', $paymentTransaction['amount']);
                    }
                } else if ($paymentTransaction->Transaction->type == "sell_return") {

                    $account->increment('balance', $paymentTransaction['amount']);
                }
            if ($paymentTransaction->Transaction->type == "purchase") {

                $account->increment('balance', $paymentTransaction['amount']);
            } else if ($paymentTransaction->Transaction->type == "purchase_return") {

                $account->decrement('balance', $paymentTransaction['amount']);
            }
        } else {
            if ($contact && !$contact_balace_no_effect)
                $contact->increment('balance', $paymentTransaction['amount']);

            if ($payment)
                $payment->decrement('amount', $paymentTransaction['amount']);

            if ($account)
                $account->decrement('balance', $paymentTransaction['amount']);
        }
        if ($payment->amount == 0) {
            $payment->delete();
        }


        $paymentTransaction->delete();
    }

    public function Update($paymentTransaction, $new_amount, $contact_balace_no_effect = false)
    {
        $old_amount = $paymentTransaction->amount;
        $new_amount = $old_amount - $new_amount;
        $contact = $paymentTransaction->contact;
        $account = $paymentTransaction->account;
        $payment = $paymentTransaction->Payment;
        $transaction = $paymentTransaction->Transaction;

        if ($paymentTransaction['operation'] == 'add') {
            if ($contact && !$contact_balace_no_effect)
                $contact->decrement('balance', $new_amount);

            if ($payment) {

                $paymentTransaction->decrement('amount', $new_amount);
                $payment->decrement('amount', $new_amount);
            }

            if ($account) {

                if ($transaction->type == "purchase") {
                    $account->increment('balance', $new_amount);
                }

                if ($transaction->type == "sell") {
                    $account->decrement('balance', $new_amount);
                }
            }
        } else {
            if ($contact && !$contact_balace_no_effect)
                $contact->increment('balance', $new_amount);

            if ($payment) {
                $payment->decrement('amount', $new_amount);
                $paymentTransaction->decrement('amount', $new_amount);
            }

            if ($account) {
                dd('test from subtract');
                $account->increment('balance', $new_amount);
            }
        }

        return $paymentTransaction;
    }

    public function ContactHistory($contact_id)
    {
        $data = [];
        $i = 0;
        $sells = Transaction::where('type', 'sell')
            ->where('contact_id', $contact_id)
            ->get();
        $purchases = Transaction::where('type', 'purchase')
            ->where('contact_id', $contact_id)
            ->get();
        $sell_returns = Transaction::with("TransactionFromReturnTransaction")->where('type', 'sell_return')
            ->where('contact_id', $contact_id)
            ->get();
        $purchase_returns = Transaction::with("TransactionFromReturnTransaction")->where('type', 'purchase_return')
            ->where('contact_id', $contact_id)
            ->get();
        $payments = Payment::with('account')
            ->where('contact_id', $contact_id)
            ->where(function ($query) {
                $query->whereNull('for')
                    ->orWhere('for', '<>', 'decrement_opening_balance');
            })
            ->get();

        // Process the opening balance
        $opening_balance = Transaction::where('type', 'opening_balance')
            ->where('contact_id', $contact_id)
            ->first();
        if ($opening_balance) {
            array_push($data, [
                'id'  => $opening_balance->id,
                'contact_name'  => $opening_balance->Contact?->name,
                'contact_type'  => $opening_balance->Contact?->type,
                'account_name'  => '',
                'label'  => 'رصيد افتتاحي',
                'amount'  => $opening_balance->final_price,
                'ref_no'  => "",
                'operation'  => 'add',
                'created_at'  => date("Y-m-d h:i a", strtotime($opening_balance->created_at)),
                'created_at_timestamp'  => $opening_balance->created_at,
                'i' => $i++,
            ]);
        }
        foreach ($sells as $sell) {
            $sell_total_with_return_total = $sell->final_price + $sell->ReturnTransactions()->sum('total');
            array_push($data, [
                'id'  => $sell->id,
                'contact_name'  => $sell->Contact?->name,
                'contact_type'  => $sell->Contact?->type,
                'account_name'  => '',
                'label'  => '<a href="#" style="color: blue;" class="fire-popup" data-url="' . route('dashboard.sells.show', $sell->id) . '" data-toggle="modal" data-target="#modal-default-big">فاتورة بيع</a>',
                'amount'  => $sell_total_with_return_total,
                'ref_no'  => $sell->ref_no,
                'operation'  => 'add',
                'created_at'  => date("Y-m-d h:i a", strtotime($sell->created_at)),
                'created_at_timestamp'  => $sell->created_at,
                'i' => $i++,
            ]);
        }
        foreach ($purchases as $purchase) {
            $purchase_total_with_return_total = $purchase->final_price + $purchase->ReturnTransactions()->sum('total');
            array_push($data, [
                'id'  => $purchase->id,
                'contact_name'  => $purchase->Contact?->name,
                'contact_type'  => $purchase->Contact?->type,
                'account_name'  => '',
                'label'  => '<a href="#"  data-toggle="modal" data-target="#modal-default-big" style="color: blue;" class="fire-popup" data-url="' . route('dashboard.purchases.show', $purchase->id) . '">فاتورة شراء</a>',
                'amount'  => $purchase_total_with_return_total,
                'ref_no'  => $purchase->ref_no,
                'operation'  => 'subtract',
                'created_at'  => date("Y-m-d h:i a", strtotime($purchase->created_at)),
                'created_at_timestamp'  => $purchase->created_at,
                'i' => $i++,
            ]);
        }
        foreach ($sell_returns as $sell_return) {
            // dd($sell_return);
            array_push($data, [
                'id'  => $sell_return->id,
                'contact_name'  => $sell_return->Contact?->name,
                'contact_type'  => $sell_return->Contact?->type,
                'account_name'  => '',
                'label'  => 'مرتجع بيع',
                'label'  => '<a href="#"  data-toggle="modal" data-target="#modal-default-big" style="color: blue;" class="fire-popup" data-url="' . route('dashboard.sells.sell-return.show', $sell_return->id) . '">مرتجع بيع</a>',
                'amount'  => $sell_return->final_price,
                'ref_no'  => $sell_return->TransactionFromReturnTransaction->ref_no,
                'operation'  => 'subtract',
                'created_at'  => date("Y-m-d h:i a", strtotime($sell_return->created_at)),
                'created_at_timestamp'  => $sell_return->created_at,
                'i' => $i++,
            ]);
        }
        foreach ($purchase_returns as $purchase_return) {
            array_push($data, [
                'id'  => $purchase_return->id,
                'contact_name'  => $purchase_return->Contact?->name,
                'contact_type'  => $purchase_return->Contact?->type,
                'account_name'  => '',
                'label'  => '<a href="#"  data-toggle="modal" data-target="#modal-default-big" style="color: blue;" class="fire-popup" data-url="' . route('dashboard.purchases.purchase-return.show', $purchase_return->id) . '">مرتجع شراء</a>',
                'amount'  => $purchase_return->final_price,
                'operation'  => 'add',
                'ref_no'  => $purchase_return->TransactionFromReturnTransaction->ref_no,
                'created_at'  => date("Y-m-d h:i a", strtotime($purchase_return->created_at)),
                'created_at_timestamp'  => $purchase_return->created_at,
                'i' => $i++,
            ]);
        }
        foreach ($payments as $Payment) {
            array_push($data, [
                'id'  => $Payment->id,
                'contact_name'  => $Payment->contact?->name,
                'contact_type'  => $Payment->contact?->type,
                'account_name'  => $Payment->account?->name,
                'label'  => 'عملية دفع',
                'amount'  => $Payment->amount,
                'operation'  => $Payment->operation,
                'ref_no'  => "",
                'created_at'  => date("Y-m-d h:i a", strtotime($Payment->created_at)),
                'created_at_timestamp'  => $Payment->created_at,
                'i' => $i++,
            ]);
        }


        usort($data, function ($a, $b) {
            return strtotime($a['created_at_timestamp']) - strtotime($b['created_at_timestamp']) ?: $a['i'] - $b['i'];
        });

        // Calculate the running balance
        $change_amount = 0;
        foreach ($data as $key => $item) {
            if ($item['operation'] === 'add') {
                $change_amount += $item['amount'];
            } else if ($item['operation'] === 'subtract') {
                $change_amount -= $item['amount'];
            }
            $data[$key]['change_amount'] = $change_amount;
        }

        // Sort the data again by creation timestamp and index in descending order
        usort($data, function ($a, $b) {
            return strtotime($b['created_at_timestamp']) - strtotime($a['created_at_timestamp']) ?: $b['i'] - $a['i'];
        });

        return [
            'data' => $data,
            'final_change_amount' => $change_amount
        ];
    }

    public function AccountHistory($account_id)
    {
        $data = [];
        //PaymentTransactions
        $Payments = Payment::with('account')
            ->where('account_id', $account_id)
            ->where(function ($query) {
                $query->whereNull('for')
                    ->orWhere('for', '<>', 'decrement_opening_balance');
            })
            ->get();

        foreach ($Payments as $Payment) {
            array_push($data, [
                'id'  => $Payment->id,
                'contact_name'  => $Payment->contact?->name,
                'contact_type'  => ($Payment->contact?->type) ? trans('admin.' . $Payment->contact?->type) : null,
                'account_name'  => $Payment->account?->name,
                'method'  => $Payment->method,
                'amount'  => $Payment->amount,
                'operation'  => $Payment->operation,
                'type'  => $Payment->type,
                'created_at'  => date("Y-m-d h:i a", strtotime($Payment->created_at)),
                'created_at_timestamp'  => $Payment->created_at,
                'created_by'  => $Payment->CreatedBy?->name,
            ]);
        }

        usort($data, function ($a, $b) {
            return strtotime($a['created_at_timestamp']) - strtotime($b['created_at_timestamp']);
        });

        //process
        $change_amount = 0;
        foreach ($data as $key => $item) {
            if ($item['operation'] === 'subtract') {
                $change_amount += $item['amount'];
            } else if ($item['operation'] === 'add') {
                $change_amount -= $item['amount'];
            }
            $data[$key]['change_amount'] = $change_amount;
        }
        usort($data, function ($a, $b) {
            return strtotime($b['created_at_timestamp']) - strtotime($a['created_at_timestamp']);
        });
        return $data;
    }
}
