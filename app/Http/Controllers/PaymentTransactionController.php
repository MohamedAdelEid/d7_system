<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Account;
use App\Models\Contact;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use APP\Services\ActivityLogsService;
use Yajra\DataTables\Facades\DataTables;
use App\Services\PaymentTransactionService;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PaymentTransactionNotification;

class PaymentTransactionController extends Controller
{
    protected $PaymentTransactionService;
    protected $ActivityLogsService;

    public function __construct(PaymentTransactionService $PaymentTransactionService, ActivityLogsService $ActivityLogsService)
    {
        $this->PaymentTransactionService = $PaymentTransactionService;
        $this->ActivityLogsService = $ActivityLogsService;
        $this->middleware('permissionMiddleware:view-payment-history-contacts')->only(['paymentHistory']);
    }
    public function pay($id)
    {  
      
        $contact = Contact::findOrFail($id);
        $totalOpeningBalance = Transaction::where('type','opening_balance')
        ->where('contact_id',  $contact->id)->where('payment_status','due')
        ->sum('final_price');
        $totalOpeningBalance = $contact->type == "supplier" ? $totalOpeningBalance * -1 : $totalOpeningBalance;
        $totalTransactionDue = Transaction::where('type','<>','opening_balance')->where('contact_id',  $contact->id)->where('payment_status','due')
        ->sum('final_price');
        $totalTransactionPartial = Transaction::where('contact_id', $contact->id)
        ->where('payment_status', 'partial')
        ->sum(DB::raw('
            ((CASE 
                WHEN final_price < 0 THEN final_price * -1 
                ELSE final_price 
            END) - (SELECT COALESCE(SUM(amount), 0) 
                     FROM payment_transactions 
                     WHERE payment_transactions.transaction_id = transactions.id))
        '));
       
       $total = $totalTransactionDue + $totalTransactionPartial + $totalOpeningBalance;
        return view('Dashboard.contacts.pay')->with([
            'contact' => $contact,
            'accounts' => Account::all(),
            'total' =>  $total

        ]);
    }
    public function payPost($id, Request $request)
    {
        
        $contact = Contact::findOrFail($id);
        $account = Account::findOrFail($request->account_id);
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
            'account_id' => 'required|exists:accounts,id',
        ]);

        $data['contact_id'] = $contact->id;
        $data['transaction_id'] = null;
        $data['operation'] = ($contact->type == 'customer') ? 'subtract' : 'add';
        try {
            DB::beginTransaction();
            $paymentTransaction = $this->PaymentTransactionService->create($data);
            $type = $contact->type == 'customer' ? 'العميل' : 'المورد';
            Notification::send(auth()->user(), new PaymentTransactionNotification($paymentTransaction, 'create', 'تم اضافة معاملة دفع جديدة بقيمة ' . $data['amount'] . ' إلى حساب ' . $account->name . ' من  ' . $type . '  ' . $contact->name . ' في ' . now()->format('F j, Y g:i A') . '.'));
        
            if ($paymentTransaction) {
                $this->ActivityLogsService->insert([
                    'subject' => $paymentTransaction,
                    'title' => 'تم إضافة معاملة دفع جديدة',
                    'description' => 'تم إضافة دفع بقيمة ' . $data['amount'] . ' إلى حساب ' . $account->name . ' من  ' . $type . '  ' . $contact->name . ' في ' . now()->format('F j, Y g:i A') . '.',
                    'proccess_type' => $contact->type == 'customer' ? 'customers' : 'suppliers',
                    'user_id' => auth()->id(),
                ]);
                DB::commit();
                return redirect()->route('dashboard.contacts.index')->with('success', 'Payment added successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to add payment');
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }



    public function paymentHistory($id, $type, Request $request)
    {  
        $supplier = Contact::where('type', 'supplier')->get();
        $customer = Contact::where('type', 'customer')->get();
        $contact = Contact::with('governorate', 'city')->findOrFail($id);
        $statistics = $this->getStatisticsByDateRange($contact, $request->date_from, $request->date_to);
        $historyResult = $this->PaymentTransactionService->ContactHistory($id);
        $data = $historyResult['data'];
        $final_change_amount = $historyResult['final_change_amount'];
        if ($request->ajax()) {

            if ($request->date_from && $request->date_to) {
                $data = $this->filterByDateRange($data, $request->date_from, $request->date_to);
            }

            return DataTables::of($data)
            ->addIndexColumn()
                ->editColumn('amount', function ($row) {
                    if ($row['operation'] == "add") {
                        return "<span class='badge badge-success'>" . $row['amount'] . "</span>";
                    }
                    if ($row['operation'] == "subtract") {
                        return "<span class='badge badge-danger'>" . $row['amount'] . "</span>";
                    }
                })
              
                ->rawColumns(['label', 'amount'])
                ->make(true);
        }
        $users = User::all();
        return view('Dashboard.contacts.payment-history', compact('contact', 'supplier', 'customer', 'type', 'users', 'statistics', 'data', 'final_change_amount'));
    }

    public function getStatisticsByDateRange($contact, $date_from, $date_to)
    {
        return $contact->getStatistics($date_from, $date_to);
    }

    public function paymentHistoryDetails($id)
    {
        $sell = Transaction::with('Contact')->where('type', 'sell')
            ->where('id', $id)
            ->get();
        return [
            'title' => trans('admin.Payment-History-Details'),
            'body'  => view('Dashboard.contacts.payment-history-details')->with([
                'sell' => $sell,
            ])->render(),
        ];
    }


    public function payTransactionView($transaction_id)
    {

        $transaction = Transaction::findOrFail($transaction_id);

        $accounts = Account::get();

        if ($transaction->payment_status  == 'final') {
            return redirect()->route('dashboard.sells.index')->with('error', 'هذه الفاتوره مدفوعه بالفعل');
        }

        $amountMustPay = 0;

        if ($transaction->payment_status  == 'final') {

            return redirect()->route('dashboard.sells.index')->with('error', 'هذه الفاتوره مدفوعه بالفعل');
        }
        if ($transaction->payment_status  == 'due') {

            $amountMustPay = $transaction->final_price;
        }
        if ($transaction->payment_status  == 'partial') {

            $amountMustPay = $transaction->final_price - $transaction->PaymentsTransaction->sum('amount');
        }


        return [
            'title' => trans('admin.Pay'),
            'body'  => view('Dashboard.sells.pay')->with([
                'amountMustPay' => $amountMustPay,
                'accounts' => $accounts,
                'transaction' => $transaction,
            ])->render(),
        ];

        // return view('Dashboard.sells.pay', compact('amountMustPay', 'accounts', 'transaction'));
    }

    public function payTransaction($transaction_id, Request $request)
    {

        $data = $request->validate([
            'amount' => 'required|numeric',
            'account_id' => 'required|exists:accounts,id',
        ]);

        $transaction = Transaction::findOrFail($transaction_id);


        if ($transaction->payment_status  == 'final') {
            return redirect()->route('dashboard.sells.index')->with('error', 'هذه الفاتوره مدفوعه بالفعل');
        }
        if ($transaction->payment_status  == 'due') {

            $amountMustPay = $transaction->final_price;
        }
        if ($transaction->payment_status  == 'partial') {

            $amountMustPay = $transaction->final_price - $transaction->PaymentsTransaction->sum('amount');
        }
        if ($data['amount'] > $amountMustPay) {

            return redirect()->route('dashboard.sells.index')

                ->with('error', 'المبلغ الذي ادخلته اكبر من اجمالي المعامله او اكبر من التبقي ');
        }

        $data['contact_id'] = $transaction->contact_id;

        $data['transaction_id'] = $transaction->id;

        $data['operation'] =  'subtract';

        $contact = Contact::find($transaction->contact_id);

        $paymentTransaction = $this->PaymentTransactionService->create($data);
        $account = Account::findOrFail($data['account_id']);
        $type = $contact->type == 'customer' ? 'العميل' : 'المورد';
        if ($paymentTransaction) {
            $this->ActivityLogsService->insert([
                'subject' => $paymentTransaction,
                'title' => 'تم إضافة معاملة دفع جديدة',
                'description' => 'تم إضافة دفع بقيمة ' . $data['amount'] . ' إلى حساب ' . $account->name . ' من  ' . $type . '  ' . $contact->name . ' في ' . now()->format('F j, Y g:i A') . '.',
                'proccess_type' => $contact->type == 'customer' ? 'customers' : 'suppliers',
                'user_id' => auth()->id(),
            ]);


            return redirect()->route('dashboard.sells.index')->with('success', 'Payment added successfully');
        }
    }
    public function payTransactionPurchasView($transaction_id)
    {

        $transaction = Transaction::findOrFail($transaction_id);

        $accounts = Account::get();

        if ($transaction->payment_status  == 'final') {
            return redirect()->route('dashboard.purchases.index')->with('error', 'هذه الفاتوره مدفوعه بالفعل');
        }

        $amountMustPay = 0;

        if ($transaction->payment_status  == 'final') {

            return redirect()->route('dashboard.purchases.index')->with('error', 'هذه الفاتوره مدفوعه بالفعل');
        }
        if ($transaction->payment_status  == 'due') {

            $amountMustPay = $transaction->final_price;
        }
        if ($transaction->payment_status  == 'partial') {

            $amountMustPay = $transaction->final_price - $transaction->PaymentsTransaction->sum('amount');
        }


        return [
            'title' => trans('admin.Pay'),
            'body'  => view('Dashboard.purchases.pay')->with([
                'amountMustPay' => $amountMustPay,
                'accounts' => $accounts,
                'transaction' => $transaction,
            ])->render(),
        ];

        // return view('Dashboard.sells.pay', compact('amountMustPay', 'accounts', 'transaction'));
    }

    public function payTransactionPurchas($transaction_id, Request $request)
    {

        $data = $request->validate([
            'amount' => 'required|numeric',
            'account_id' => 'required|exists:accounts,id',
        ]);

        $transaction = Transaction::findOrFail($transaction_id);


        if ($transaction->payment_status  == 'final') {
            return redirect()->route('dashboard.purchases.index')->with('error', 'هذه الفاتوره مدفوعه بالفعل');
        }
        if ($transaction->payment_status  == 'due') {

            $amountMustPay = $transaction->final_price;
        }
        if ($transaction->payment_status  == 'partial') {

            $amountMustPay = $transaction->final_price - $transaction->PaymentsTransaction->sum('amount');
        }
        if ($data['amount'] > $amountMustPay) {

            return redirect()->route('dashboard.purchases.index')

                ->with('error', 'المبلغ الذي ادخلته اكبر من اجمالي المعامله او اكبر من التبقي ');
        }

        $data['contact_id'] = $transaction->contact_id;

        $data['transaction_id'] = $transaction->id;

        $data['operation'] =  'add';

        $contact = Contact::find($transaction->contact_id);

        $paymentTransaction = $this->PaymentTransactionService->create($data);
        $account = Account::findOrFail($data['account_id']);
        $type = $contact->type == 'customer' ? 'العميل' : 'المورد';
        Notification::send(auth()->user(), new PaymentTransactionNotification($paymentTransaction, 'create', 'تم اضافة معاملة دفع جديدة بقيمة ' . $data['amount'] . ' إلى حساب ' . $account->name . ' من  ' . $type . '  ' . $contact->name . ' في ' . now()->format('F j, Y g:i A') . '.'));

        if ($paymentTransaction) {
            $this->ActivityLogsService->insert([
                'subject' => $paymentTransaction,
                'title' => 'تم إضافة معاملة دفع جديدة',
                'description' => 'تم إضافة دفع بقيمة ' . $data['amount'] . ' إلى حساب ' . $contact->name . '.',
                'user_id' => auth()->id(),
            ]);


            return redirect()->route('dashboard.purchases.index')->with('success', 'Payment added successfully');
        }
    }
    public function filterByDateRange($data, $date_from, $date_to)
    {

        $dateFrom = $date_from ? Carbon::parse($date_from) : null;

        $dateTo = $date_to ? Carbon::parse($date_to) : null;


        $filteredData = array_filter($data, function ($item) use ($dateFrom, $dateTo) {

            $createdAt = Carbon::parse($item['created_at']);

            return $createdAt->between($dateFrom, $dateTo);

            if ($dateFrom && $dateTo) {
                return $createdAt->between($dateFrom, $dateTo);
            } elseif ($dateFrom) {
                return $createdAt->gte($dateFrom);
            } elseif ($dateTo) {
                return $createdAt->lte($dateTo);
            }

            return true; // No filter, return all
        });

        return $filteredData;
    }


    public function payPopup($id)
    {
   

        $contact = Contact::findOrFail($id);
        $totalOpeningBalance = Transaction::where('type','opening_balance')
        ->where('contact_id',  $contact->id)->where('payment_status','due')
        ->sum('final_price');
        $totalOpeningBalance = $contact->type == "supplier" ? $totalOpeningBalance * -1 : $totalOpeningBalance;
        $totalTransactionDue = Transaction::where('type','<>','opening_balance')->where('contact_id',  $contact->id)->where('payment_status','due')
        ->sum('final_price');
        $totalTransactionPartial = Transaction::where('contact_id', $contact->id)
        ->where('payment_status', 'partial')
        ->sum(DB::raw('
            ((CASE 
                WHEN final_price < 0 THEN final_price * -1 
                ELSE final_price 
            END) - (SELECT COALESCE(SUM(amount), 0) 
                     FROM payment_transactions 
                     WHERE payment_transactions.transaction_id = transactions.id))
        '));
        $total = $totalTransactionDue + $totalTransactionPartial + $totalOpeningBalance;
        return [
            'title' => trans('admin.Pay'),
            'body'  => view('Dashboard.contacts.pay-popup')->with([
                'contact' => $contact,
                'accounts' => Account::all(),
                'total' =>  $total,
            ])->render(),
        ];
    }
    public function payPopupPost($id, Request $request)
    {   
        
        $contact = Contact::findOrFail($id);
        $data = $request->validate([
            'amount' => 'required|numeric',
            'account_id' => 'required|exists:accounts,id',
        ]);

        $data['contact_id'] = $contact->id;
        $data['transaction_id'] = null;
        $data['operation'] = ($contact->type == 'customer') ? 'subtract' : 'add';
        try {
            DB::beginTransaction();
            $paymentTransaction = $this->PaymentTransactionService->create($data);
            $account = Account::findOrFail($data['account_id']);
            $type = $contact->type == 'customer' ? 'العميل' : 'المورد';
            Notification::send(auth()->user(), new PaymentTransactionNotification($paymentTransaction, 'create', 'تم اضافة معاملة دفع جديدة بقيمة ' . $data['amount'] . ' إلى حساب ' . $account->name . ' من  ' . $type . '  ' . $contact->name . ' في ' . now()->format('F j, Y g:i A') . '.'));
            DB::commit();
            if ($paymentTransaction) {
                $this->ActivityLogsService->insert([
                    'subject' => $paymentTransaction,
                    'title' => 'تم إضافة معاملة دفع جديدة',
                    'description' => 'تم إضافة دفع بقيمة ' . $data['amount'] . ' إلى حساب ' . $account->name . ' من  ' . $type . '  ' . $contact->name . ' في ' . now()->format('F j, Y g:i A') . '.',
                    'proccess_type' => $contact->type == 'customer' ? 'customers' : 'suppliers',
                    'user_id' => auth()->id(),
                ]);
    
                return redirect()->back()->with('success', 'Payment added successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to add payment');
            }
        } catch (\Exception $e)
        {
            DB::rollback();

            throw $e;
        }
       
    }
}
