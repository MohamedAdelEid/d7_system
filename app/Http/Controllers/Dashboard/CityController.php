<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\City;
use App\Models\Governorate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{

    public function __construct() 
    {
        $this->middleware('permissionMiddleware:read-cities')->only('index');
        $this->middleware('permissionMiddleware:delete-cities')->only('destroy');
        $this->middleware('permissionMiddleware:update-cities')->only(['edit', 'update']);
        $this->middleware('permissionMiddleware:create-cities')->only(['create', 'store']);
    }


    public function index(Request $request){
        if ($request->ajax()) {
            $data = City::query();

            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn =  '<div class="btn-group"><button type="button" class="btn btn-success">'. trans('admin.Actions') .'</button><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"></button><div class="dropdown-menu" role="menu">';

                        if (auth('user')->user()->has_permission('update-cities'))
                            $btn .= '<a class="dropdown-item" href="' . route('dashboard.cities.edit', $row->id).'">' . trans("admin.Edit") . '</a>';

                        if (auth('user')->user()->has_permission('delete-cities'))
                            $btn .= '<a class="dropdown-item delete-popup" href="#" data-toggle="modal" data-target="#modal-default" data-url="'.route("dashboard.cities.destroy", $row->id).'">' . trans('admin.Delete') . '</a>';
                        
                        $btn.= '</div></div>';
                        return $btn;
                    })
                    ->addColumn('governorate', function($row){
                        return $row->governorate->governorate_name_ar;
                    })
                    ->addColumn('name', function($row){
                        return $row->city_name_ar;
                    })
               
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('Dashboard.cities.index');
    }

    public function create(){
        $governorates = Governorate::get();
        return view('Dashboard.cities.create' , compact('governorates'));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'governorate_id' => 'required|exists:governorates,id', // Ensure it exists in the governorates table
            'city_name_ar'   => 'required|string|max:255|unique:cities,city_name_ar', // Unique Arabic city name
            'city_name_en'   => 'required|string|max:255|unique:cities,city_name_en', // Unique Arabic city name
        ]);

        if ($validator->fails()) {
            return redirect()->route('dashboard.cities.index')->with(['errors' => $validator->errors()]);
        }

        City::create([
            'governorate_id' => $request->governorate_id,
            'city_name_ar'   => $request->city_name_ar,
            'city_name_en'   => $request->city_name_en,
        ]);

        return redirect()->route('dashboard.cities.index')->with('success', 'success');
    }

    public function edit($id)
    {
        $city = City::findOrFail($id); 
        $governorates = Governorate::all(); 

        return view('Dashboard.cities.edit', compact('city', 'governorates'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'governorate_id' => 'required|exists:governorates,id',
            'city_name_ar'   => 'required|string|max:255|unique:cities,city_name_ar,' . $id, // Allow the same city name for the current city
            'city_name_en'   => 'required|string|max:255|unique:cities,city_name_en,' . $id, // Allow the same city name for the current city
        ]);

        if ($validator->fails()) {
            return redirect()->route('dashboard.cities.edit', $id)->withErrors($validator)->withInput();
        }

        $city = City::findOrFail($id);
        $city->update([
            'governorate_id' => $request->governorate_id,
            'city_name_ar'   => $request->city_name_ar,
            'city_name_en'   => $request->city_name_en,
        ]);

        return redirect()->route('dashboard.cities.index')->with('success', 'City updated successfully');
    }

    public function destroy($id)
    {
        $city = City::findOrFail($id);
        $city->delete(); 

        return redirect()->route('dashboard.cities.index')->with('success', 'City deleted successfully');
    }

}
