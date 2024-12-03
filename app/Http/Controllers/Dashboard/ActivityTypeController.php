<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\ActivityType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class ActivityTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ActivityType::query();

            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn =  '<div class="btn-group"><button type="button" class="btn btn-success">'. trans('admin.Actions') .'</button><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"></button><div class="dropdown-menu" role="menu">';

                        if (auth('user')->user()->has_permission('update-activityTypes'))
                            $btn .= '<a class="dropdown-item" href="' . route('dashboard.activityTypes.edit', $row->id).'">' . trans("admin.Edit") . '</a>';

                        if (auth('user')->user()->has_permission('delete-activityTypes'))
                            $btn .= '<a class="dropdown-item delete-popup" href="#" data-toggle="modal" data-target="#modal-default" data-url="'.route("dashboard.activityTypes.destroy", $row->id).'">' . trans('admin.Delete') . '</a>';
                        
                        $btn.= '</div></div>';
                        return $btn;
                    })
                    ->addColumn('name', function($row){
                        return $row->name;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('Dashboard.activityTypes.index');
    }

    public function create()
    {
        return view('Dashboard.activityTypes.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:activity_types,name',
        ]);

        if ($validator->fails()) {
            return redirect()->route('dashboard.activityTypes.index')->with(['errors' => $validator->errors()]);
        }

        ActivityType::create([
            'name' => $request->name,
        ]);

        return redirect()->route('dashboard.activityTypes.index')->with('success', 'activityType created successfully');
    }

    public function edit($id)
    {
        $activityType = ActivityType::findOrFail($id);
        return view('Dashboard.activityTypes.edit', compact('activityType'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:activity_types,name,' . $id,
        ]);

        if ($validator->fails()) {
            return redirect()->route('dashboard.activityTypes.edit', $id)->withErrors($validator)->withInput();
        }

        $activityType = ActivityType::findOrFail($id);
        $activityType->update([
            'name' => $request->name,
        ]);

        return redirect()->route('dashboard.activityTypes.index')->with('success', 'activityType updated successfully');
    }

    public function destroy($id)
    {
        $activityType = ActivityType::findOrFail($id);
        $activityType->delete();

        return redirect()->route('dashboard.activityTypes.index')->with('success', 'activityType deleted successfully');
    }
    
}
