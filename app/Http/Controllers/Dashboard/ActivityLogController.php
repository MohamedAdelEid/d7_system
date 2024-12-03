<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use App\Models\Contact;
use App\Models\Expense;
use App\Models\Transaction;
use App\Models\Activity_log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class ActivityLogController extends Controller
{ 
    public function index(Request $request)
    {
        if($request->ajax()){
            $activities = Activity_log::query()->orderBy('id', 'desc');
            if($request->user_id){
                $activities->where('user_id', $request->user_id);
            }
            if ($request->date_from && $request->date_to) {
                $activities->whereBetween('created_at', [$request->date_from, $request->date_to]);
            }
            if ($request->proccess_type) {
                $activities->where('proccess_type', $request->proccess_type);
            }
            return DataTables::of($activities)
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group"><button type="button" class="btn btn-success">' . trans('admin.Actions') . '</button><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"></button><div class="dropdown-menu" role="menu">';
                    if (auth('user')->user()->has_permission('read-activity-logs'))
                        $btn .= '<a class="dropdown-item" href="' . route('dashboard.activity-log.show', $row->id) . '">' . trans('admin.Show') . '</a>';
                    $btn .= '</div></div>';
                    return $btn;
                })
                ->addColumn('title', function ($row) {
                    return $row->title;
                })
                ->addColumn('user', function ($row) {
                    return $row->user->name;
                })
                ->addColumn('proccess_type', function ($row) {
                    return $row->proccess_type;
                })
                ->addColumn('description', function ($row) {
                    return $row->description;
                })
                ->addColumn('created_at', function ($row) {
                    return \Carbon\Carbon::parse($row->created_at)->format('F j, Y g:i A');
                })
                ->addIndexColumn()
                ->make(true);
        }
        $users = User::get();
        return view('Dashboard.activity-log.index', compact('users'));
    }
}
