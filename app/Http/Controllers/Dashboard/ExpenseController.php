<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use App\Models\Branch;
use App\Models\Account;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogsService;
use Yajra\DataTables\Facades\DataTables;
use App\Notifications\ExpensesNotification;
use App\Services\PaymentTransactionService;
use Illuminate\Support\Facades\Notification;

class ExpenseController extends Controller
{
    protected $PaymentTransactionService;
    protected $activityLogService;
    public function __construct(ActivityLogsService $activityLogService,PaymentTransactionService $PaymentTransactionService)
    {
        $this->PaymentTransactionService = $PaymentTransactionService;
        $this->activityLogService = $activityLogService;

    }
    public function index(Request $request)
    {
        if($request->ajax()){

            $expenses = Expense::with(['expenseCategory', 'account', 'branch'])->orderBy('created_at', 'desc');

        if ($request->branch_id) {
            $expenses->where('branch_id', $request->branch_id);
        }
        if ($request->expense_category_id) {
            $expenses->where('expense_category_id', $request->expense_category_id);
        }
            return DataTables::of($expenses)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn =  '<div class="btn-group"><button type="button" class="btn btn-success">'. trans('admin.Actions') .'</button><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"></button><div class="dropdown-menu" role="menu">';
                    if (auth('user')->user()->has_permission('read-expenses')) {
                        $btn .= '<a class="dropdown-item fire-popup" data-url="' . route('dashboard.expenses.show', $row->id) . '"
                                href="#" data-toggle="modal" data-target="#modal-default-big">' . trans("admin.Show") . '</a>';
                    }
                    //my menu
                    if (auth('user')->user()->has_permission('update-expenses')) {
                        $btn .= '<a class="dropdown-item" href="' . route('dashboard.expenses.edit', $row->id).'">' . trans("admin.Edit") . '</a>';
                    }

                    if (auth('user')->user()->has_permission('delete-expenses')) {
                        $btn .= '<a class="dropdown-item delete-popup" href="#" data-toggle="modal" data-target="#modal-default" data-url="'.route("dashboard.expenses.destroy", $row->id).'">' . trans('admin.Delete') . '</a>';
                    }

                    $btn.= '</div></div>';
                    return $btn;
                })
                ->addColumn('expense_category', function($row){
                    return $row->expenseCategory?->name;
                })
                ->addColumn('account', function($row){
                    return $row->account?->name;
                })
                ->addColumn('branch', function($row){
                    return $row->branch?->name;
                })
                ->addColumn('amount', function($row){
                    return $row->amount;
                })
                ->addColumn('route', function ($row) {
                    return route('dashboard.expenses.show', $row->id);
                })
                ->addColumn('created_by', function($row){
                    return $row->createdBy?->name;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $branches = Branch::all();
        $expenseCategories = ExpenseCategory::all();
        return view('Dashboard.expenses.index', compact('branches', 'expenseCategories'));
    }

    public function create()
    {
        $expenseCategories = ExpenseCategory::all();
        $accounts = Account::all();
        $branches = Branch::all();
        $users = User::all();
        return view('Dashboard.expenses.create', compact('expenseCategories', 'accounts', 'branches','users'));
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'expense_category_id' => 'required|integer|exists:expense_categories,id',
            'created_by' => 'required|integer|exists:users,id',
            'account_id' => 'required|integer|exists:accounts,id',
            'branch_id' => 'required|integer|exists:branchs,id',
            'amount' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    $account = Account::find($request->account_id);
                    if ($account && $value > $account->balance) {
                        $fail('لا يمكن أن يكون المبلغ أكبر من الرصيد المتاح في الحساب: ' . $account->balance);
                    }
                }
            ],
            'note' => 'nullable|string',
        ]);
        $account = Account::find($validatedData['account_id']);
        $payment_data = [
            'account_id'     => $request->account_id,
            'amount'         => $validatedData['amount'],
            'operation'         => 'add',
            'type'            => 'expense',
            'created_by'      => auth()->id(),
        ];
        $this->PaymentTransactionService->create($payment_data);
        $expense = Expense::create($validatedData);
        Notification::send(auth()->user(), new ExpensesNotification($expense, 'create', 'تم اضافة مصروف جديد بقيمة ' . $validatedData['amount'] . " ضمن الفرع " . $expense->branch->name . "  من الحساب  " . $account->name .' في تاريخ  ' . \Carbon\Carbon::parse($expense->created_at)->format('Y-m-d') .'بواسطة ' . $expense->createdBy->name));
        $this->activityLogService->insert([
            'subject' => $expense,
            'title' => 'تم إضافة مصروف جديد',
            'description' => 'تم إضافة مصروف جديد بقيمة ' . $validatedData['amount'] . " ضمن الفرع " . $expense->branch->name . " من الحساب  " . $account->name,
            'proccess_type' => 'expenses',
            'user_id' => auth()->id(),
        ]);
        
        

        return redirect()->route('dashboard.expenses.index')->with('success', trans('admin.CreateExpense'));
    }

    public function edit($id)
    {
        $expense = Expense::find($id);
        $expenseCategories = ExpenseCategory::all();
        $accounts = Account::all();
        $branches = Branch::all();
        $users = User::all();
        return view('Dashboard.expenses.edit', compact('expense', 'expenseCategories', 'accounts', 'branches','users'));
    }

    public function update(Request $request, $id)
{
    // Validate the incoming request data
    $validatedData = $request->validate([
        'expense_category_id' => 'required|integer|exists:expense_categories,id',
        'account_id' => 'required|integer|exists:accounts,id',
        'branch_id' => 'required|integer|exists:branchs,id',
        'amount' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    $account = Account::find($request->account_id);
                    if ($account && $value > $account->balance) {
                        $fail('لا يمكن أن يكون المبلغ أكبر من الرصيد المتاح في الحساب: ' . $account->balance);
                    }
                }
            ],        'note' => 'nullable|string',
    ],[
        'amount.lte' => 'لا يمكن أن يكون المبلغ أكبر من الرصيد المتاح في الحساب',
    ]);

    // Find the existing expense and account
    $expense = Expense::find($id);
    $account = Account::find($validatedData['account_id']);

    // Check if the new amount is greater or less than the previous amount
    if ($validatedData['amount'] > $expense->amount) {
        // Case 1: New amount is greater than the old amount
        // Decrease the account balance by the difference
        $account->decrement('balance', $validatedData['amount'] - $expense->amount);
    } elseif ($validatedData['amount'] < $expense->amount) {
        // Case 2: New amount is less than the old amount
        // Increase the account balance by the difference
        $account->increment('balance', $expense->amount - $validatedData['amount']);
    }

    // Update the expense with the new data
    $expense->update($validatedData);
    Notification::send(auth()->user(), new ExpensesNotification($expense, 'update', 'تم تحديث مصروف بقيمة ' .  $validatedData['amount'] . " ضمن الفرع " . $expense->branch->name . "  من الحساب  " . $account->name .' في تاريخ  ' . \Carbon\Carbon::parse($expense->created_at)->format('Y-m-d') .'بواسطة ' . $expense->createdBy->name));
    // Log the activity of the expense update
    $this->activityLogService->insert([
        'subject' => $expense,
        'title' => 'تم تحديث مصروف',
        'description' => 'تم تحديث مصروف بقيمة ' .  $validatedData['amount'] . " ضمن الفرع " . $expense->branch->name . " من الحساب  " . $account->name,
        'proccess_type' => 'expenses',
        'user_id' => auth()->id(),
    ]);

    // Redirect back to the expenses index with a success message
    return redirect()->route('dashboard.expenses.index')->with('success', trans('admin.updateExpense'));
}


    public function destroy($id)
    {
        $expense = Expense::find($id);
        $payment_data = [
            'account_id'     => $expense->account_id,
            'amount'         => $expense->amount,
            'operation'         => 'subtract',
        ];
        $this->PaymentTransactionService->create($payment_data);
        $expense->delete();
        Notification::send(auth()->user(), new ExpensesNotification($expense, 'delete', 'تم حذف مصروف بقيمة ' . $expense->amount . " ضمن الفرع " . $expense->branch->name . " من الحساب  " . $expense->account->name));
        $this->activityLogService->insert([
            'subject' => $expense,
            'title' => 'تم حذف مصروف',
            'description' => 'تم حذف مصروف بقيمة ' . $expense->amount . " ضمن الفرع " . $expense->branch->name . " من الحساب  " . $expense->account->name,
            'proccess_type' => 'expenses',
            'user_id' => auth()->id(),
        ]);
        return redirect()->route('dashboard.expenses.index')->with('success', trans('admin.Deleted'));
    }
    public function show($id)
    {
        $expense = Expense::find($id);
        return [
            'title' => trans('admin.Show'),
            'body'  => view('Dashboard.expenses.show')->with([
                'expense' => $expense,
            ])->render(),
        ];
    }
}
