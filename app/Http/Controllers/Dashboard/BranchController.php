<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\City;
use App\Models\Branch;
use App\Models\Account;
use App\Models\Governorate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogsService; 
use Yajra\DataTables\Facades\DataTables;
use App\Notifications\BranchNotification;
use Illuminate\Support\Facades\Notification;

class BranchController extends Controller
{
    public $activityLogsService;
    public function __construct(ActivityLogsService $activityLogsService) {
        $this->middleware('permissionMiddleware:read-branchs')->only('index');
        $this->middleware('permissionMiddleware:delete-branchs')->only('destroy');
        $this->middleware('permissionMiddleware:update-branchs')->only(['edit', 'update']);
        $this->middleware('permissionMiddleware:create-branchs')->only(['create', 'store']);
        $this->activityLogsService = $activityLogsService;
    }

    public function index(Request $request){
        if ($request->ajax()) {
            $data = Branch::query();

            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn =  '<div class="btn-group"><button type="button" class="btn btn-success">'. trans('admin.Actions') .'</button><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"></button><div class="dropdown-menu" role="menu">';

                        if (auth('user')->user()->has_permission('update-branchs'))
                            $btn .= '<a class="dropdown-item" href="' . route('dashboard.branchs.edit', $row->id).'">' . trans("admin.Edit") . '</a>';

                        if (auth('user')->user()->has_permission('delete-branchs'))
                            $btn .= '<a class="dropdown-item delete-popup" href="#" data-toggle="modal" data-target="#modal-default" data-url="'.route("dashboard.branchs.destroy", $row->id).'">' . trans('admin.Delete') . '</a>';
                        
                        $btn.= '</div></div>';
                        return $btn;
                    })
                    ->addColumn('cash_account', function($row){
                        return $row->CreditAccount?->name;
                    })
                    ->addColumn('credit_account', function($row){
                        return $row->CreditAccount?->name;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('Dashboard.branchs.index');
    }

    public function create(){
        $accounts = Account::get();
        $governorates = Governorate::get();
        $cities = [];
        return view('Dashboard.branchs.create')->with([
            'accounts'  => $accounts,
            'governorates'  => $governorates,
            'cities'  => $cities
        ]);
    }

    public function store(Request $request){
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'cash_account_id' => 'required|exists:accounts,id',
            'credit_account_id' => 'required|exists:accounts,id',
            'governorate_id' => 'required|exists:governorates,id',
            'city_ids' => 'required|array',
            'city_ids.*' => 'exists:cities,id',
        ]);

        $branch = Branch::create([
            'name' => $validatedData['name'],
            'cash_account_id' => $validatedData['cash_account_id'],
            'credit_account_id' => $validatedData['credit_account_id'],
            'governorate_id' => $validatedData['governorate_id'],
        ]);

        $branch->cities()->sync($validatedData['city_ids']);
        $input = $request->only('name','cash_account_id','credit_account_id');
        // $branch = Branch::create($input);
        Notification::send(auth()->user(), new BranchNotification($branch, 'create', 'تم اضافة الفرع ' . $branch->name . ' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($branch->created_at)->format('Y-m-d')));
        $this->activityLogsService->insert([
            'subject'     => $branch,  
            'title' => 'تم إضافة الفرع',
            'description' => 'تم إضافة الفرع ' . $branch->name,
            'proccess_type' => 'create',
            'user_id'     => auth()->id(),  
            ]);
        return redirect(route('dashboard.branchs.index'))->with('success', 'success');
    }

    public function edit($id){
        $branch = Branch::findOrFail($id);
        $accounts = Account::get();
        $governorates = Governorate::get();
        $selectedCities = $branch->cities()->pluck('city_id')->toArray();
        $cities = [];
        $cities = City::all();
        return view('Dashboard.branchs.edit')->with([
            'data' => $branch,
            'accounts'  => $accounts,
            'governorates'  => $governorates,
            'selectedCities'  => $selectedCities,
            'cities'  => $cities
        ]);
    }

    public function update($id, Request $request){
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'cash_account_id' => 'required|exists:accounts,id',
            'credit_account_id' => 'required|exists:accounts,id',
            'governorate_id' => 'required|exists:governorates,id',
            'city_ids' => 'required|array',
            'city_ids.*' => 'exists:cities,id',
        ]);

        $branch = Branch::findOrFail($id);
        
        $branch->update([
            'name' => $validatedData['name'],
            'cash_account_id' => $validatedData['cash_account_id'],
            'credit_account_id' => $validatedData['credit_account_id'],
            'governorate_id' => $validatedData['governorate_id'],
        ]);

        // تحديث ربط المدن بالفرع
        $branch->cities()->sync($validatedData['city_ids']);
            $this->activityLogsService->insert([
                'subject'     => $branch,  
                'title' => 'تم تعديل الفرع',
                'description' => 'تم تعديل الفرع ' . $branch->name,
                'proccess_type' => 'update',
                'user_id'     => auth()->id(),  
                ]);
        // $branch->update($input);
        Notification::send(auth()->user(), new BranchNotification($branch, 'update', 'تم تعديل الفرع ' . $branch->name . ' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($branch->updated_at)->format('Y-m-d')));
        $this->activityLogsService->insert([
            'subject'     => $branch,  
            'title' => 'تم تعديل الفرع',
            'description' => 'تم تعديل الفرع ' . $branch->name,
            'proccess_type' => 'update',
            'user_id'     => auth()->id(),  
            ]);
        return redirect(route('dashboard.branchs.index'))->with('success', 'success');
    }

    public function destroy($brand_id){
        $branch = Branch::findOrFail($brand_id);
        $branch->delete();
        Notification::send(auth()->user(), new BranchNotification($branch, 'delete', 'تم حذف الفرع ' . $branch->name . ' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($branch->deleted_at)->format('Y-m-d')));
        $this->activityLogsService->insert([
            'subject'     => $branch,  
            'title' => 'تم حذف الفرع',
            'description' => 'تم حذف الفرع ' . $branch->name,
            'proccess_type' => 'delete',
            'user_id'     => auth()->id(),  
            ]);
        return redirect()->back()->with('success', trans('admin.success'));
    }

    public function getCitiesByGovernorate(Request $request) {

        $governorate_id = $request->input('governorate_id'); // Get the governorate ID from the request
    
        // Validate the governorate ID
        if (!$governorate_id) {
            return response()->json(['error' => 'Governorate ID is required'], 400);
        }
    
        // Fetch cities based on the governorate ID
        $cities = City::where('governorate_id', $governorate_id)->get();
    
        return response()->json($cities);
    }
}
