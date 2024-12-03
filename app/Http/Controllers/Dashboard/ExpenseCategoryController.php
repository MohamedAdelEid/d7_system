<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogsService;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ExpensesCategoryNotification;

class ExpenseCategoryController extends Controller
{
    protected $ActivityLogsService;
    public function __construct(ActivityLogsService $activityLogsService)
    {
        $this->ActivityLogsService = $activityLogsService;
    }

    public function index(Request $request){
        if ($request->ajax()) {
            $data = ExpenseCategory::query();

            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn =  '<div class="btn-group"><button type="button" class="btn btn-success">'. trans('admin.Actions') .'</button><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"></button><div class="dropdown-menu" role="menu">';

                        //my menu
                        if (auth('user')->user()->has_permission('update-expense-categories')) {
                            $btn .= '<a class="dropdown-item" href="' . route('dashboard.expense-categories.edit', $row->id).'">' . trans("admin.Edit") . '</a>';
                        }

                        if (auth('user')->user()->has_permission('delete-expense-categories')) {
                            $btn .= '<a class="dropdown-item delete-popup" href="#" data-toggle="modal" data-target="#modal-default" data-url="'.route("dashboard.expense-categories.destroy", $row->id).'">' . trans('admin.Delete') . '</a>';
                        }
                        
                        $btn.= '</div></div>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('Dashboard.expense-categories.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Dashboard.expense-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

      $expense_category =   ExpenseCategory::create($validated);
        Notification::send(auth()->user(), new ExpensesCategoryNotification($expense_category, 'create', 'تم اضافة فئة مصروف جديدة بأسم '.$expense_category->name .'.'));
        $this->ActivityLogsService->insert([
            'subject' => $expense_category,
            'title' => 'تم إضافة فئة مصروف جديدة',
            'description' => 'تم اضافة فئة مصروف جديدة بأسم '.$expense_category->name .'.',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('dashboard.expense-categories.index')->with('success', 'Success');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $expense_category = ExpenseCategory::find($id);
        return view('Dashboard.expense-categories.edit', ['data' => $expense_category]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $expense_category = ExpenseCategory::find($id);
        $expense_category->update($validated);
        Notification::send(auth()->user(), new ExpensesCategoryNotification($expense_category, 'update', 'تم تحديث فئة مصروف جديدة بأسم '.$expense_category->name .'.'));
        $this->ActivityLogsService->insert([
            'subject' => $expense_category,
            'title' => 'تم تحديث فئة مصروف',
            'description' => 'تم تحديث فئة مصروف جديدة بأسم '.$expense_category->name .'.',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('dashboard.expense-categories.index')->with('success', trans('admin.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $expense_category = ExpenseCategory::find($id);
        $expense_category->delete();
        Notification::send(auth()->user(), new ExpensesCategoryNotification($expense_category, 'delete', 'تم حذف فئة مصروف جديدة بأسم '.$expense_category->name .'.'));
        $this->ActivityLogsService->insert([
            'subject' => $expense_category,
            'title' => 'تم حذف فئة مصروف',
            'description' => 'تم حذف فئة مصروف جديدة بأسم '.$expense_category->name .'.',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('dashboard.expense-categories.index')->with('success', trans('admin.Deleted'));
    }
}
