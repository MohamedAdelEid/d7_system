<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Branch;
use App\Models\Account;
use Illuminate\Http\Request;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLogsService;
use Yajra\DataTables\Facades\DataTables;
use App\Notifications\AccountNotification;
use App\Services\PaymentTransactionService;
use Illuminate\Support\Facades\Notification;


class AccountCountroller extends Controller
{
    protected $PaymentTransactionService;
    protected $ActivityLogsService;
    public function __construct(PaymentTransactionService $PaymentTransactionService, ActivityLogsService $ActivityLogsService)
    {
        $this->middleware('permissionMiddleware:read-accounts')->only('index');
        $this->middleware('permissionMiddleware:delete-accounts')->only('destroy');
        $this->middleware('permissionMiddleware:update-accounts')->only(['edit', 'update']);
        $this->middleware('permissionMiddleware:create-accounts')->only(['create', 'store']);

        $this->PaymentTransactionService = $PaymentTransactionService;
        $this->ActivityLogsService = $ActivityLogsService;
    }

    public function index(Request $request)
    {

        if ($request->ajax()) {

            $data = Account::query()->orderBy('created_at','desc');

            if ($request->type)
                $data->where('type', $request->type);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn =  '<div class="btn-group"><button type="button" class="btn btn-success">' . trans('admin.Actions') . '</button><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"></button><div class="dropdown-menu" role="menu">';

                    //my menu
                    if (auth('user')->user()->has_permission('update-accounts'))
                        $btn .= '<a class="dropdown-item" href="' . route('dashboard.accounts.edit', $row->id) . '">' . trans("admin.Edit") . '</a>';

                    if (auth('user')->user()->has_permission('delete-accounts'))
                        $btn .= '<a class="dropdown-item delete-popup" href="#" data-toggle="modal" data-target="#modal-default" data-url="' . route("dashboard.accounts.destroy", $row->id) . '">' . trans('admin.Delete') . '</a>';

                    if (auth('user')->user()->has_permission('show-transaction-history'))
                        $btn .= '<a class="dropdown-item" href="' . route('dashboard.accounts.transaction-history', $row->id) . '">' . trans("admin.transaction_history") . '</a>';

                    if (auth('user')->user()->has_permission('add-deposit'))
                        $btn .= '<a class="dropdown-item" href="' . route('dashboard.accounts.add-deposit', $row->id) . '">' . trans("admin.Add_Deposit") . '</a>';

                    if (auth('user')->user()->has_permission('transfer-money'))
                        $btn .= '<a class="dropdown-item" href="' . route('dashboard.accounts.transfer', $row->id) . '">' . trans("admin.Transfer") . '</a>';

                    if (auth('user')->user()->has_permission('change-status'))
                        $btn .= '<a class="dropdown-item fire-popup" data-url="' . route('dashboard.accounts.change-status', $row->id) . '"
                        href="#" data-toggle="modal" data-target="#modal-default">' . trans("admin.Change_Status") . '</a>';

                    $btn .= '</div></div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('Dashboard.accounts.index');
    }

    public function create()
    {
        return view('Dashboard.accounts.create');
    }

    public function store(Request $request)
    {
        $input = $request->only('name', 'number');
        $account = Account::create($input);
        Notification::send(auth()->user(), new AccountNotification($account, 'create', 'تم اضافة حساب جديد ' . $account->name . ' بواسطة ' . auth()->user()->name));
        $this->ActivityLogsService->insert([
            'subject' => $account,
            'title' => 'تم اضافة حساب جديد ',
            'description' => 'الحساب ' . $account->name . ' تم اضافته بنجاح .',
            'user_id' => auth()->id(),
        ]);
        return redirect('dashboard/accounts')->with('success', 'success');
    }

    public function edit($id)
    {
        $account = Account::findOrFail($id);

        return view('Dashboard.accounts.edit')->with([
            'data' => $account,
        ]);
    }

    public function update($id, Request $request)
    {
        $account = Account::findOrFail($id);
        $input = $request->only('name', 'number');

        $account->update($input);
        $account->save();
        Notification::send(auth()->user(), new AccountNotification($account, 'update', 'تم تعديل الحساب ' . $account->name . ' بواسطة ' . auth()->user()->name));
        $this->ActivityLogsService->insert([
            'subject' => $account,
            'title' => 'تم تعديل الحساب' . $account->name,
            'description' => 'الحساب ' . $account->name . ' تم تعديله بنجاح .',
            'user_id' => auth()->id(),
        ]);
        return redirect('dashboard/accounts')->with('success', 'success');
    }

    public function destroy($account_id)
    {
        $account = Account::findOrFail($account_id);

        $account->delete();
        Notification::send(auth()->user(), new AccountNotification($account, 'delete', 'تم حذف الحساب ' . $account->name . ' بواسطة ' . auth()->user()->name));
        $this->ActivityLogsService->insert([
            'subject' => $account,
            'title' => 'تم حذف حساب بنجاح',
            'description' => 'الحساب ' . $account->name . 'تم حذفه بنجاح .',
            'user_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', trans('admin.success'));
    }

    public function transactionHistory($account_id, Request $request)
    {


        $account = Account::findOrFail($account_id);
        if ($request->ajax()) {
            if ($request->created_by) {
                $data = $this->PaymentTransactionService->AccountHistory($account_id, $request->created_by);
            } else {
                $data = $this->PaymentTransactionService->AccountHistory($account_id);
            }

            if ($request->date_from && $request->date_to) {
                $data = $this->filterByDateRange($data, $request->date_from, $request->date_to);
            }

            return DataTables::of($data)
                ->editColumn('amount', function ($row) {
                    if ($row['operation'] == "add") {
                        return "<span class='badge badge-success'>" . $row['amount'] . "</span>";
                    }
                    if ($row['operation'] == "subtract") {
                        return "<span class='badge badge-danger'>" . $row['amount'] . "</span>";
                    }
                })
                ->rawColumns(['amount'])
                // ->addColumn('type', function($row){
                //     return trans('admin.' . $row['type']);
                // })
                ->make(true);
        }
        $users = User::all();
        return view('Dashboard.accounts.transaction-history')->with([
            'account' => $account,
            'users' => $users,
        ]);
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
    public function addDeposit($account_id)
    {
        $account = Account::findOrFail($account_id);
        return view('Dashboard.accounts.add-deposit')->with([
            'account' => $account,
        ]);
    }

    public function addDepositPost($account_id, Request $request)
    {
        $account = Account::findOrFail($account_id);
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);
        try {
            DB::beginTransaction();
            $payment_data = [
                'account_id'     => $account_id,
                'amount'         => $request->amount,
                'operation' => 'subtract',
            ];
            $this->PaymentTransactionService->create($payment_data);
            DB::commit();
            Notification::send(auth()->user(), new AccountNotification($account, 'add-deposit', 'تم اضافة ايداع بقيمة ' . $request->amount . ' إلى حساب ' . $account->name . ' بواسطة ' . auth()->user()->name));
            $this->ActivityLogsService->insert([
                'subject' => $account,
                'title' => 'تم تنفيذ ايداع',
                'description' => 'تم إضافة إيداع بقيمة ' . $request->amount . ' إلى حساب ' . $account->name . ' بنجاح.',
                'user_id' => auth()->id(),
            ]);
            return redirect()->route('dashboard.accounts.index')->with('success', trans('admin.success'));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', trans('admin.error'));
        }
    }

    public function transferForm($account_id)
    {
        $account = Account::findOrFail($account_id);
        return view('Dashboard.accounts.transfer')->with([
            'account' => $account,
        ]);
    }
    public function transferMoney(Request $request)
    {
        $validatedData = $request->validate([
            'from_account' => 'required|exists:accounts,id',
            'to_account' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0',
        ], [
            'from_account.required' => 'The from account field is required.',
            'to_account.required' => 'The to account field is required.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount field must be a number.',
            'amount.min' => 'The amount field must be greater than 0.',
            'from_account.exists' => 'The selected account is invalid.',
            'to_account.exists' => 'The selected account is invalid.',
        ]);

        DB::beginTransaction();
        $payment_data = [
            'account_id'     => $request->from_account,
            'amount'         => $request->amount,
            'operation'         => 'add',
        ];
        $this->PaymentTransactionService->create($payment_data);

        $payment_data = [
            'account_id'     => $request->to_account,
            'amount'         => $request->amount,
            'operation' => 'subtract',
        ];
        $account = $this->PaymentTransactionService->create($payment_data);
        $from_account = Account::find($validatedData['from_account']);
        $to_account = Account::find($validatedData['to_account']);
        DB::commit();
        Notification::send(auth()->user(), new AccountNotification($from_account, 'transfer-money', 'تم عملية تحويل للاموال من حساب ' . $from_account->name . ' إلى حساب ' . $to_account->name . ' بقيمة ' . $request->amount . ' بواسطة ' . auth()->user()->name));
        $this->ActivityLogsService->insert([
            'subject' => $account,
            'title' => 'تم عملية تحويل للاموال',
            'description' => 'تم نقل المال من حساب ' . $from_account->name . ' إلى حساب ' . $to_account->name . ' بنجاح. المبلغ: ' . $request->amount,
            'user_id' => auth()->id(),
        ]);
        return redirect()->route('dashboard.accounts.index')->with('success', trans('admin.success'));
    }

    public function AccountByBranch()
    {
        $accounts = Branch::find(Request()->branch_id)->Accounts;

        return view('Dashboard.accounts.AccountByBranch')->with([
            'accounts' => $accounts
        ]);
    }
    public function changeStatus($account_id)
    {
        $account = Account::findOrFail($account_id);
        return [
            'title' => trans('admin.Change_Status'),
            'body' => view('Dashboard.accounts.change-status')->with([
                'account' => $account,
            ])->render(),
        ];
    }
    
    public function changeStatusPost($account_id, Request $request)
    {
        $account = Account::findOrFail($account_id);
        $account->update(['is_active' => $request->status]);
        Notification::send(auth()->user(), new AccountNotification($account, 'change-status', 'تم تعديل حالة الحساب ' . $account->name . ' بواسطة ' . auth()->user()->name));
        $this->ActivityLogsService->insert([
            'subject' => $account,
            'title' => 'تم تعديل حالة الحساب',
            'description' => 'تم تعديل حالة الحساب ' . $account->name . ' بنجاح.',
            'user_id' => auth()->id(),
        ]);
        return redirect()->back()->with('success', trans('admin.success'));
    }
  
}
