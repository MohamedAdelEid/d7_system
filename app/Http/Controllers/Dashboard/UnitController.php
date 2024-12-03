<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Unit;
use App\Traits\Stock;
use App\Models\Contact;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogsService;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{
    use Stock;
    public $activityLogsService;
    
    public function __construct(ActivityLogsService $activityLogsService) {
        $this->middleware('permissionMiddleware:read-units')->only('index');
        $this->middleware('permissionMiddleware:delete-units')->only('destroy');
        $this->middleware('permissionMiddleware:update-units')->only(['edit', 'update']);
        $this->middleware('permissionMiddleware:create-units')->only(['create', 'store']);
        $this->activityLogsService = $activityLogsService;

    }

    public function index(Request $request){
        if ($request->ajax()) {
            $data = Unit::query();

            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn =  '<div class="btn-group"><button type="button" class="btn btn-success">'. trans('admin.Actions') .'</button><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"></button><div class="dropdown-menu" role="menu">';

                        //my menu
                        if (auth('user')->user()->has_permission('update-units'))
                            $btn .= '<a class="dropdown-item" href="' . route('dashboard.units.edit', $row->id).'">' . trans("admin.Edit") . '</a>';

                        if (auth('user')->user()->has_permission('delete-units'))
                            $btn .= '<a class="dropdown-item delete-popup" href="#" data-toggle="modal" data-target="#modal-default" data-url="'.route("dashboard.units.destroy", $row->id).'">' . trans('admin.Delete') . '</a>';
                        
                        $btn.= '</div></div>';
                        return $btn;
                    })
                    ->addColumn('base_unit', function($row){
                        if($row->Unit)
                            return $row->Unit->actual_name;

                        return '';
                    })
                    ->addColumn('base_unit_multiplier', function($row){
                        if($row->base_unit_id != null){
                            if($row->base_unit_is_largest == 0)
                                return $row->base_unit_multiplier . $row->getUnit();

                            return '1/' . $row->base_unit_multiplier . $row->getUnit();
                        }
                        return '';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('Dashboard.units.index');
    }

    public function create(){
        $roles = Unit::all();
        $base_units = Unit::main()->get();

        return view('Dashboard.units.create')->with([
            "base_units" => $base_units,
            "roles" => $roles
        ]);
    }
    public function updateUnit(Request $request)
    {
        $productId = $request->input('product_id');
        $unitId = $request->input('unit_id');
        $branchId = $request->input('branch_id');
        $contactId = $request->input('contact_id');
      
        $unit = Unit::find($unitId);
        $contact = Contact::find($contactId); 
        $product = Product::with('MainUnit', 'salesSegments')->find($productId);
        
        if (!$unit || !$product || !$contact) {
            return response()->json(['success' => false]);
        }
        $product = Product::find($request->product_id);
        $contact = Contact::find($request->contact_id);

     

        $contactSalesSegment = $contact->salesSegment->id ?? "";
        
      
    
        $newUnitPrice = $product->getSalePriceByUnitAndSegment($unitId, $contactSalesSegment) 
        ?? $product->getSalePriceByUnit($unitId); 
    
        $newUnitPricePurchase = $product->getPurchasePriceByUnit($unit->id)
         ?? $product->getPurchasePriceByUnit($unitId);
        
        $min_sale = $this->getQuantityByUnit($product, $unitId, $product->min_sale);
        $max_sale = $this->getQuantityByUnit($product, $unitId, $product->max_sale);
   
        if ($unitId == $product->MainUnit->id) {
            $availableQuantity = $product->getStockByBranch($branchId);
        } else {
            $availableQuantity = $this->getQuantityByUnit($product, $unitId, $product->getStockByBranch($branchId));
        }
      
        $last_sale_price = $product->TransactionSellLines()
        ->whereHas('Transaction', function ($query) use ($contactId, $branchId) {
            
            $query->where('contact_id', $contactId)
            ->where('branch_id', $branchId);
        })
        ->where('unit_id', $unitId)
        ->latest('created_at') 
        ->first()->unit_price ?? false;
     
        return response()->json([
            'success' => true,
            'new_unit_price' => $newUnitPrice,
            'new_unit_price_purchase' => $newUnitPricePurchase,
            'unit_multipler' => $unit->base_unit_multiplier,
            'available_quantity' => $availableQuantity,
            'min_sale' => $min_sale,
            'max_sale' => $max_sale,
            'last_sale_price' => $last_sale_price,
        ]);
    }
    public function updateUnitStock(Request $request)
    {
        $productId = $request->input('product_id');
        $unitId = $request->input('unit_id');
        $branchId = $request->input('branch_id');

        $unit = Unit::find($unitId);
        $product = Product::with('MainUnit', 'salesSegments')->find($productId);

        if (!$unit || !$product) {
            return response()->json(['success' => false]);
        }

        $min_sale = $this->getQuantityByUnit($product, $unitId, $product->min_sale);
        $max_sale = $this->getQuantityByUnit($product, $unitId, $product->max_sale);

        if ($unitId == $product->MainUnit->id) {
            $availableQuantity = $product->getStockByBranch($branchId);
        } else {
            $availableQuantity = $this->getQuantityByUnit($product, $unitId, $product->getStockByBranch($branchId));
        }

        return response()->json([
            'success' => true,
            'unit_multipler' => $unit->base_unit_multiplier,
            'available_quantity' => $availableQuantity,
            'min_sale' => $min_sale,
            'max_sale' => $max_sale,
        ]);
    }
    private function getQuantityByUnit($product, $unitId, $quantity)
    {
        $unit = $product->units->find($unitId);

        if (!$unit || !$unit->base_unit_multiplier) {
            return $quantity;
        }

        return $quantity * $unit->base_unit_multiplier;
    }

    
    

    public function store(Request $request){
        $input = $request->except('_token', 'is_sub_unit');

        if(!$request->base_unit_is_largest)
            $input['base_unit_is_largest'] = 0;
        
        $unit = Unit::create($input);

        if(!$request->is_sub_unit){
            $unit->base_unit_id = null;
            $unit->base_unit_multiplier = null;
            $unit->base_unit_is_largest = 0;
            $unit->save();
        }
        $this->activityLogsService->insert([
            'subject'     => $unit,  
            'title' => 'تم إضافة الوحدة',
            'description' => 'تم إضافة الوحدة ' . $unit->actual_name,
            'proccess_type' => 'create',
            'user_id'     => auth()->id(),  
            ]);
        return redirect('dashboard/units')->with('success', 'success');
    }

    public function edit($id){
        $data = Unit::findOrFail($id);
        $base_units = Unit::main()
                            ->where('id', '!=', $id)
                            ->get();

        return view('Dashboard.units.edit')->with([
            'data' => $data,
            'base_units'    => $base_units
        ]);
    }

    public function update($id, Request $request){
        $data = Unit::findOrFail($id);
        $input = $request->except('_token', 'is_sub_unit');

        if(!$request->base_unit_is_largest)
            $input['base_unit_is_largest'] = 0;

        $data->update($input);

        if(!$request->is_sub_unit){
            $data->base_unit_id = null;
            $data->base_unit_multiplier = null;
            $data->base_unit_is_largest = 0;
            $data->save();
        }
        $this->activityLogsService->insert([
            'subject'     => $data,  
            'title' => 'تم تعديل الوحدة',
            'description' => 'تم تعديل الوحدة ' . $data->actual_name,
            'proccess_type' => 'update',
            'user_id'     => auth()->id(),  
            ]);
        return redirect('dashboard/units')->with('success', 'success');
    }

    public function destroy($user_id){
        $data = Unit::findOrFail($user_id);

        $data->delete();
        $this->activityLogsService->insert([
            'subject'     => $data,  
            'title' => 'تم حذف الوحدة',
            'description' => 'تم حذف الوحدة ' . $data->actual_name,
            'proccess_type' => 'delete',
            'user_id'     => auth()->id(),  
            ]);
        return redirect()->back()->with('success', trans('admin.success'));
    }

    public function subUnitsAjax(Request $request){
        $units = Unit::where(function($query) use ($request) {
                            $query->where('base_unit_id', $request->unit_id);
                            $query->orWhere('id', $request->unit_id);
                        })
                        ->get();

        return view('Dashboard.units.partial.sub-units-select')->with([
                    'units'    => $units
                ]);
    }

    public function getSubUnits(Request $request)
{
    $baseUnitId = $request->input('base_unit_id');
    
    $subUnits = Unit::where('base_unit_id', $baseUnitId)->get();
    
    return response()->json($subUnits);
}
}
