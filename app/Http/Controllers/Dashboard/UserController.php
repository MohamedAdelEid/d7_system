<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\users\createRequest;
use App\Http\Requests\users\editRequest;
use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogsService;
use App\Services\UsersService;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    protected $UsersService;
    protected $ActivityLogsService;

    public function __construct(UsersService $UsersService, ActivityLogsService $ActivityLogsService) {
        $this->UsersService = $UsersService;
        $this->ActivityLogsService = $ActivityLogsService;

        $this->middleware('permissionMiddleware:read-users')->only('index');
        $this->middleware('permissionMiddleware:delete-users')->only('destroy');
        $this->middleware('permissionMiddleware:update-users')->only(['edit', 'update', 'activity_logs']);
        $this->middleware('permissionMiddleware:create-users')->only(['create', 'store']);
    }

    public function index(Request $request){
        $roles = Role::get();

        if ($request->ajax()) {
            $data = User::where('super', '!=', 1);

            if($request->role){
                $data->whereRoleIs($request->role);
            }

            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn =  '<div class="btn-group"><button type="button" class="btn btn-success">'. trans('admin.Actions') .'</button><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"></button><div class="dropdown-menu" role="menu">';

                        //my menu
                        if (auth('user')->user()->has_permission('update-users')) {
                            $btn .= '<a class="dropdown-item" href="' . route('dashboard.users.edit', $row->id).'">' . trans("admin.Edit") . '</a>';
                        }

                        if (auth('user')->user()->has_permission('update-users')) {
                            $btn .= '<a class="dropdown-item" href="' . route('dashboard.users.activityLogs', $row->id) .'">' . trans('admin.Activity logs') . '</a>';
                        }

                        if (auth('user')->user()->has_permission('delete-users')) {
                            $btn .= '<a class="dropdown-item delete-popup" href="#" data-toggle="modal" data-target="#modal-default" data-url="'.route("dashboard.users.destroy", $row->id).'">' . trans('admin.Delete') . '</a>';
                        }
                        
                        $btn.= '</div></div>';
                        return $btn;
                    })
                    ->addColumn('role', function($row){
                        return $row->getRole();
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('Dashboard.users.index')->with([
            'roles' => $roles,
        ]);
    }

    public function create(){
        $roles = Role::all();
        $branches = Branch::active()->get();

        return view('Dashboard.users.create')->with([
            'roles' => $roles,
            'branches'  => $branches
        ]);
    }

    public function store(createRequest $request){
        $user = $this->UsersService->insert($request);
        $this->ActivityLogsService->insert([
            'subject'     => $user, 
            'title'       => 'تم انشاء مستخدم: '.$user->name,
            'description' => 'تم انشاء مستخدم جديد: ' . $user->name . ' (اسم المستخدم: ' . $user->username . ') وتم تعيينه الي الفرع: ' . $user->mainBranch->name . ' بتاريخ ' . now()->format('F j, Y g:i A') . ' مع دور: ' . $user->getRole(),
            'proccess_type' => 'create',
            'user_id'     => auth()->id(), 
        ]);
        return redirect(route('dashboard.users.index'))->with('success', 'success');
    }

    public function edit($id){
        $roles = Role::all();
        $user = User::findOrFail($id);
        $branches = Branch::active()->get();

        if($user->super == 1)
            return redirect(route('dashboard.users.index'))->with('error', trans('admin.you can\'t update this user'));
        
            return view('Dashboard.users.edit')->with([
                'roles' => $roles,
                'data' => $user,
                'branches' => $branches,
                'selectedBranch' => $user->branch_id,
            ]);
    }

    public function update($id, editRequest $request){
        $user = User::findOrFail($id);
        $this->UsersService->update($user, $request);
        $this->ActivityLogsService->insert([
            'subject'     => $user, 
            'title'       => 'تم تعديل مستخدم: '.$user->name,
            'description' => 'تم تعديل مستخدم: ' . $user->name . ' (اسم المستخدم: ' . $user->username . ') بتاريخ ' . now()->format('F j, Y g:i A') . ' مع دور: ' . $user->getRole(),
            'proccess_type' => 'update',
            'user_id'     => auth()->id(), 
        ]);
        return redirect(route('dashboard.users.index'))->with('success', 'success');
    }

    public function destroy($user_id){
        $user = User::findOrFail($user_id);

        if($user->super == 1)
            return redirect(route('dashboard.users.index'))->with('error', trans('admin.you can\'t delete this user'));
        
        $user->delete();

        $this->ActivityLogsService->insert([
            'subject'     => $user, 
            'title'       => 'تم حذف مستخدم: '.$user->name,
            'description' => 'تم حذف مستخدم: ' . $user->name . ' (اسم المستخدم: ' . $user->username . ') بتاريخ ' . now()->format('F j, Y g:i A'),
            'proccess_type' => 'delete',
            'user_id'     => auth()->id(), 
        ]);

        return redirect()->back()->with('success', trans('admin.success'));
    }

    public function activity_logs($id){
        $user = User::findOrFail($id);
    
        return view('Dashboard.users.activity_logs')->with([
            'user' => $user,
        ]);
    }
}