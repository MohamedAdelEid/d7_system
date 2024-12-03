<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Governorate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class GovernorateController extends Controller
{


    public function __construct() 
    {
        $this->middleware('permissionMiddleware:read-governorates')->only('index');
        $this->middleware('permissionMiddleware:delete-governorates')->only('destroy');
        $this->middleware('permissionMiddleware:update-governorates')->only(['edit', 'update']);
        $this->middleware('permissionMiddleware:create-governorates')->only(['create', 'store']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Governorate::query();

            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn =  '<div class="btn-group"><button type="button" class="btn btn-success">'. trans('admin.Actions') .'</button><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"></button><div class="dropdown-menu" role="menu">';

                        if (auth('user')->user()->has_permission('update-governorates'))
                            $btn .= '<a class="dropdown-item" href="' . route('dashboard.governorates.edit', $row->id).'">' . trans("admin.Edit") . '</a>';

                        if (auth('user')->user()->has_permission('delete-governorates'))
                            $btn .= '<a class="dropdown-item delete-popup" href="#" data-toggle="modal" data-target="#modal-default" data-url="'.route("dashboard.governorates.destroy", $row->id).'">' . trans('admin.Delete') . '</a>';
                        
                        $btn.= '</div></div>';
                        return $btn;
                    })
                    ->addColumn('name', function($row){
                        return $row->governorate_name_ar;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('Dashboard.governorates.index');
    }

    public function create()
    {
        return view('Dashboard.governorates.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'governorate_name_ar' => 'required|string|max:255|unique:governorates,governorate_name_ar',
            'governorate_name_en' => 'required|string|max:255|unique:governorates,governorate_name_en',
        ]);

        if ($validator->fails()) {
            return redirect()->route('dashboard.governorates.create')->withErrors($validator);
        }

        Governorate::create([
            'governorate_name_ar' => $request->governorate_name_ar,
            'governorate_name_en' => $request->governorate_name_en,
        ]);

        return redirect()->route('dashboard.governorates.index')->with('success', 'Governorate created successfully');
    }

    public function edit($id)
    {
        $governorate = Governorate::findOrFail($id);
        return view('Dashboard.governorates.edit', compact('governorate'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'governorate_name_ar' => 'required|string|max:255|unique:governorates,governorate_name_ar,' . $id,
            'governorate_name_en' => 'required|string|max:255|unique:governorates,governorate_name_en,' . $id,
        ]);

        if ($validator->fails()) {
            return redirect()->route('dashboard.governorates.edit', $id)->withErrors($validator);
        }

        $governorate = Governorate::findOrFail($id);
        $governorate->update([
            'governorate_name_ar' => $request->governorate_name_ar,
            'governorate_name_en' => $request->governorate_name_en,
        ]);

        return redirect()->route('dashboard.governorates.index')->with('success', 'Governorate updated successfully');
    }

    public function destroy($id)
    {
        $governorate = Governorate::findOrFail($id);
        $governorate->delete();

        return redirect()->route('dashboard.governorates.index')->with('success', 'Governorate deleted successfully');
    }
}
