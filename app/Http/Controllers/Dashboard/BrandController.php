<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogsService; 
use App\Notifications\BrandNotification;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Notification;

class BrandController extends Controller
{
    protected $activityLogsService;
    public function __construct(ActivityLogsService $activityLogsService) {
        $this->middleware('permissionMiddleware:read-brands')->only('index');
        $this->middleware('permissionMiddleware:delete-brands')->only('destroy');
        $this->middleware('permissionMiddleware:update-brands')->only(['edit', 'update']);
        $this->middleware('permissionMiddleware:create-brands')->only(['create', 'store']);
        $this->activityLogsService = $activityLogsService; 
    }

    public function index(Request $request){
        if ($request->ajax()) {
            $data = Brand::query();

            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn =  '<div class="btn-group"><button type="button" class="btn btn-success">'. trans('admin.Actions') .'</button><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"></button><div class="dropdown-menu" role="menu">';

                        //my menu
                        if (auth('user')->user()->has_permission('update-brands')) {
                            $btn .= '<a class="dropdown-item" href="' . route('dashboard.brands.edit', $row->id).'">' . trans("admin.Edit") . '</a>';
                        }

                        if (auth('user')->user()->has_permission('delete-brands')) {
                            $btn .= '<a class="dropdown-item delete-popup" href="#" data-toggle="modal" data-target="#modal-default" data-url="'.route("dashboard.brands.destroy", $row->id).'">' . trans('admin.Delete') . '</a>';
                        }
                        
                        $btn.= '</div></div>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('Dashboard.brands.index');
    }

    public function create(){
        return view('Dashboard.brands.create');
    }

    public function store(Request $request){
        $input = $request->only('name');
        $brand = Brand::create($input);

        if ($request->hasFile('image')) {
            $brand->addMedia($request->file('image'))->toMediaCollection('brands');
        } 
        if ($request->hasFile('cover')) {
            $brand->addMedia($request->file('cover'))->toMediaCollection('brand_cover');
        } 
        Notification::send(auth()->user(), new BrandNotification($brand, 'create', 'تم اضافة العلامة التجارية ' . $brand->name . ' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($brand->created_at)->format('Y-m-d')));
        $this->activityLogsService->insert([
            'subject'     => $brand,
            'title' => 'تم إضافة العلامة التجارية',
            'description' => 'تم إضافة العلامة التجارية ' . $brand->name,
            'proccess_type' => 'create',
            'user_id'     => auth()->id(),
        ]);

        return redirect(route('dashboard.brands.index'))->with('success', 'success');
    }

    public function edit($id){
        $brand = Brand::findOrFail($id);
        
        return view('Dashboard.brands.edit')->with([
            'data' => $brand,
        ]);
    }

    public function update($id, Request $request){
        $input = $request->only('name');
        $brand = Brand::findOrFail($id);
        $brand->update($input);

        if ($request->hasFile('image')) {
            $brand->clearMediaCollection('image'); // Clear the existing logo
            $brand->addMedia($request->file('image'))->toMediaCollection('brands');
        }
        if ($request->hasFile('cover')) {
            $brand->clearMediaCollection('brand_cover'); // Clear the existing logo
            $brand->addMedia($request->file('cover'))->toMediaCollection('brand_cover');
        }
        Notification::send(auth()->user(), new BrandNotification($brand, 'update', 'تم تعديل العلامة التجارية ' . $brand->name . ' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($brand->updated_at)->format('Y-m-d')));
        $this->activityLogsService->insert([
            'subject'     => $brand,
            'title' => 'تم تعديل العلامة التجارية',
            'description' => 'تم تعديل العلامة التجارية ' . $brand->name,
            'proccess_type' => 'update',
            'user_id'     => auth()->id(),
        ]);
        return redirect(route('dashboard.brands.index'))->with('success', 'success');
    }

    public function destroy($brand_id){
        $brand = Brand::findOrFail($brand_id);
        $brand->delete();
        Notification::send(auth()->user(), new BrandNotification($brand, 'delete', 'تم حذف العلامة التجارية ' . $brand->name . ' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($brand->deleted_at)->format('Y-m-d')));
        $this->activityLogsService->insert([
            'subject'     => $brand,
            'title' => 'تم حذف العلامة التجارية',
            'description' => 'تم حذف العلامة التجارية ' . $brand->name,
            'proccess_type' => 'delete',
            'user_id'     => auth()->id(),
        ]);
        return redirect()->back()->with('success', trans('admin.success'));
    }
}
