<?php

namespace App\Http\Controllers;

use App\Models\SalesSegment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogsService;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SalesSegmentNotification;

class SalesSegmentController extends Controller
{
    protected $activityLogsService;
    public function __construct(ActivityLogsService $activityLogsService)
    {
        $this->activityLogsService = $activityLogsService;
    }
    public function index(Request $request){
        
        if($request->ajax()){
            $data = SalesSegment::query()->orderBy('id', 'desc')->get();
            return DataTables::of($data)
            ->addColumn('action', function($row){
                $btn =  '<div class="btn-group"><button type="button" class="btn btn-success">'. trans('admin.Actions') .'</button><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"></button><div class="dropdown-menu" role="menu">';

                //my menu
                if (auth('user')->user()->has_permission('update-sales-segments')) {
                    $btn .= '<a class="dropdown-item" href="' . route('dashboard.sales-segments.edit', $row->id).'">' . trans("admin.Edit") . '</a>';
                }

                if (auth('user')->user()->has_permission('delete-sales-segments')) {
                    $btn .= '<a class="dropdown-item delete-popup" href="#" data-toggle="modal" data-target="#modal-default" data-url="'.route("dashboard.sales-segments.destroy", $row->id).'">' . trans('admin.Delete') . '</a>';
                }
                
                $btn.= '</div></div>';
                return $btn;
            })
            ->addColumn('name', function($row){
                return $row->name;
            })
            ->addColumn('description', function($row){
                return $row->description;
            })
            ->addColumn('created_at', function($row){
                return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i');
            })
            ->rawColumns(['action', 'checkbox'])
            ->make(true);
        }
        return view('Dashboard.sales-segments.index');
    }
    public function create(){
        return view('Dashboard.sales-segments.create');
    }
    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $salesSegment = SalesSegment::create($request->all());
        Notification::send(auth()->user(), new SalesSegmentNotification($salesSegment, 'create', 'تم اضافة شريحة بيع جديدة بأسم '.$salesSegment->name .' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($salesSegment->created_at)->format('Y-m-d')));
        $this->activityLogsService->insert([
            'subject'     => $salesSegment,  
            'title' => 'تم إضافة شريحة بيع جديدة',
            'description' => 'تم إضافة شريحة بيع جديدة ' . $salesSegment->name,
            'proccess_type' => 'create',
            'user_id'     => auth()->id(),  
            ]);
        return redirect()->route('dashboard.sales-segments.index')->with('success', trans('admin.sales_segment_created_successfully'));
    }
    public function edit($id){
        $salesSegment = SalesSegment::find($id);
        return view('Dashboard.sales-segments.edit', compact('salesSegment'));
    }
    public function update(Request $request, $id){
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $salesSegment = SalesSegment::find($id);
        $salesSegment->update($request->all());
        Notification::send(auth()->user(), new SalesSegmentNotification($salesSegment, 'update', 'تم تعديل شريحة بيع بأسم '.$salesSegment->name .' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($salesSegment->created_at)->format('Y-m-d')));
        $this->activityLogsService->insert([
            'subject'     => $salesSegment,  
            'title' => 'تم تعديل شريحة بيع',
            'description' => 'تم تعديل شريحة بيع ' . $salesSegment->name,
            'proccess_type' => 'update',
            'user_id'     => auth()->id(),  
            ]);
        return redirect()->route('dashboard.sales-segments.index')->with('success', trans('admin.sales_segment_updated_successfully'));
    }
    public function destroy($id){
        $salesSegment = SalesSegment::find($id);
        $salesSegment->delete();
        Notification::send(auth()->user(), new SalesSegmentNotification($salesSegment, 'delete', 'تم حذف شريحة بيع بأسم '.$salesSegment->name .' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($salesSegment->created_at)->format('Y-m-d')));
        $this->activityLogsService->insert([
            'subject'     => $salesSegment,  
            'title' => 'تم حذف شريحة بيع',
            'description' => 'تم حذف شريحة بيع ' . $salesSegment->name,
            'proccess_type' => 'delete',
            'user_id'     => auth()->id(),  
            ]);
        return redirect()->route('dashboard.sales-segments.index')->with('success', trans('admin.sales_segment_deleted_successfully'));
    }
}
