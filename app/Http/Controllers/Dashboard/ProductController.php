<?php

namespace App\Http\Controllers\Dashboard;

use Exception;
use App\Models\Unit;
use OpenStockImport;
use App\Models\Brand;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Category;
use App\Models\SalesSegment;
use Illuminate\Http\Request;
use App\Models\ProductBranch;
use App\Services\StockService;
use App\Imports\ProductsImport;
use App\Services\ProductService;
use App\Services\PurchaseService;
use App\Models\ProductUnitDetails;
use Illuminate\Support\Facades\DB;
use App\Models\ProductPriceHistory;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ProductBranchDetails;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\ActivityLogsService;
use Yajra\DataTables\Facades\DataTables;
use App\Notifications\ProductNotification;
use Illuminate\Support\Facades\Notification;

class ProductController extends Controller
{
    public $ProductService;
    public $PurchaseService;
    public $StockService;
    public $activityLogsService;
    public function __construct(
        ProductService $ProductService,
        StockService $StockService,
        PurchaseService $PurchaseService,
        ActivityLogsService $activityLogsService
    ) {
        $this->ProductService = $ProductService;
        $this->StockService = $StockService;
        $this->PurchaseService = $PurchaseService;
        $this->activityLogsService = $activityLogsService;
        $this->middleware('permissionMiddleware:read-products')->only('index');
        $this->middleware('permissionMiddleware:delete-products')->only('destroy');
        $this->middleware('permissionMiddleware:update-products')->only(['edit', 'update']);
        $this->middleware('permissionMiddleware:create-products')->only(['create', 'store']);
        $this->middleware('permissionMiddleware:import-products')->only(['import', 'importView']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
        
            $data = Product::query()->orderBy('id', 'desc');

            if ($request->brand_id) {
                $data->where('brand_id', $request->brand_id);
            }

            if ($request->category_id) {
                $data->where('category_id', $request->category_id);
            }
            if ($request->branch_id) {
                $data->whereHas('Branches', function ($query) use ($request) {
                    $query->where('branch_id', $request->branch_id);
                });
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group"><button type="button" class="btn btn-success">' . trans('admin.Actions') .
                        '</button><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"></button><div class="dropdown-menu" role="menu">';

                    //menu
                    $btn .= '<a class="dropdown-item fire-popup" data-url="' . route('dashboard.products.show', $row->id) . '"
                        href="#" data-toggle="modal" data-target="#modal-default-big">' . trans("admin.Show") . '</a>';
                    if (auth('user')->user()->has_permission('update-products'))
                        $btn .= '<a class="dropdown-item" href="' . route('dashboard.products.edit', $row->id) . '">' . trans("admin.Edit") . '</a>';

                    if (auth('user')->user()->has_permission('delete-products'))
                        $btn .= '<a class="dropdown-item delete-popup" href="#" data-toggle="modal" data-target="#modal-default" data-url="' . route("dashboard.products.destroy", $row->id) . '">' . trans('admin.Delete') . '</a>';

                    if (auth('user')->user()->has_permission('openStock-products') && $row->Branches && count($row->Branches) > 0)
                        $btn .= '<a class="dropdown-item" href="' . route('dashboard.products.openStock', $row->id) . '">' . trans("admin.openStock") . '</a>';

                    if (auth('user')->user()->has_permission('history-products'))
                        $btn .= '<a class="dropdown-item" href="' . route('dashboard.products.history', $row->id) . '">' . trans("admin.history") . '</a>';
                    $btn .= '</div></div>';
                    return $btn;
                })
                ->editColumn('unit', function ($row) {
                
                    if ($row->MainUnit)
                        return $row->MainUnit?->actual_name;

                    return '';
                })
                ->addColumn('branches', function ($row) {
                    return $row->Branches->pluck('name')->implode(', ');
                })
                ->addColumn('sell_price', function ($row) {
                    return $row->getSellPrice() ?? '';
                })
                ->addColumn('purchase_price', function ($row) {
                    return $row->getPurchasePrice() ?? '';
                })
                ->addColumn('route', function ($row) {
                    return route('dashboard.products.show', $row->id);
                })
                ->addColumn('image', function ($row) {
                    return "<image src='" . $row->getImage() . "' style='width:100px'>";
                })
                ->editColumn('brand', function ($row) {
                    if ($row->Brand)
                        return $row->Brand->name;

                    return '';
                })
                ->editColumn('category', function ($row) {
                    if ($row->Category)
                        return $row->Category->name;

                    return '';
                })
                ->editColumn('qty_available_by_main_unit', function ($row) {
                    $text = '';
                    if (!$row->Branches) {
                        return $text;
                    }

                    foreach ($row->Branches as $branch) {
                        $branchQuantity = $row->getStockByMainUnit($branch->id); // Assuming 'getStock' fetches the stock for each branch
                        $text .= $branch->name . ': ' . $branchQuantity . ' ' . ($row->MainUnit ? $row->MainUnit->actual_name : '') . '<br>';
                    }

                    return $text;
                })
                ->editColumn('qty_available_by_sub_unit', function ($row) {
                    $text = '';
            
                    if (!$row->Branches) {
                        return $text;
                    }
                
                    foreach ($row->Branches as $branch) {
                        $stockDetails = $row->getStockBySubUnit($branch->id);
                    
                        if (!$stockDetails) {
                            $text .= $branch->name . ': N/A<br>';
                            continue;
                        }
                    
                        $stockInSubUnit = $stockDetails['stock_in_sub_unit'];
                        $subUnitName = $stockDetails['sub_unit_details']->actual_name ?? 'N/A';
                    
                        $text .= $branch->name . ': ' . $stockInSubUnit . ' ' . $subUnitName . '<br>';
                    }
                
                    return $text;
                })                
                ->addColumn('bulk_edit', function ($row) {
                    if (auth('user')->user()->has_permission('bulk-edit')) {
                        return '<input type="checkbox" name="bulk_edit[]" value="' . $row->id . '" class="bulk-edit-checkbox">';
                    }
                    return '';
                })
                ->rawColumns(['action', 'image', 'qty_available_by_main_unit', 'qty_available_by_sub_unit', 'bulk_edit'])
                ->make(true);
        }
        $categories = Category::all();
        $brands = Brand::all();
        $branches = Branch::active()->get();
        return view('Dashboard.products.index', compact(['categories', 'brands', 'branches']));
    }

    public function ProductRowAdd(Request $request)
    {
        $product = Product::with('units')->find($request->product_id);
        $product_row = [
            'id' => $product->id,
            'name' => $product->name,
            'unit_price' => $product->unit_price,
            'available_quantity' => $product->available_quantity,
            'quantity' => 1, // Default to 1 when first added
            'units' => $product->units, // Assuming you have units relation in Product model
            'total' => $product->unit_price, // Default total = price * quantity (1)
        ];

        // Return the rendered HTML for the product row
        return view('Dashboard.products.create', compact('product_row'))->render();
    }

    public function create()
    {
        $settings = Setting::first();
        $brands = Brand::get();
        $main_units = Unit::main()->get();
        $main_categories = Category::main()->get();
        $sub_categories = [];
        $sub_units = [];
        $Branches = Branch::active()->get();
        $salesSegments = SalesSegment::all();
        return view('Dashboard.products.create')
            ->with([
                'brands' => $brands,
                'main_units' => $main_units,
                'main_categories' => $main_categories,
                'sub_categories' => $sub_categories,
                'sub_units' => $sub_units,
                'Branches' => $Branches,
                'salesSegments' => $salesSegments,
                'settings' => $settings
            ]);
    }

    public function searchProducts(Request $request)
    {
        $branchId = $request->input('branch_id');
        $searchTerm = $request->input('query');

        // Fetch products based on the branch and search term
        $products = Product::where('branch_id', $branchId)
            ->where('name', 'LIKE', "%{$searchTerm}%")
            ->get();

        // Return the products as a JSON response
        return response()->json($products);
    }

    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|integer|unique:products,sku',
            'description' => 'nullable|string|max:255',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'main_category_id' => 'nullable|integer|exists:categories,id',
            'category_id' => 'nullable|integer|exists:categories,id',
            'unit_id' => 'required|integer|exists:units,id',
            'sub_unit_ids' => 'nullable|array',
            'sub_unit_ids.*' => 'integer|exists:units,id',
            'units' => 'required|array',
            'units.*.sale_price' => 'required|numeric',
            'units.*.purchase_price' => 'required|numeric',
            'units.*.sales_segments' => 'nullable|array',
            'units.*.sales_segments.*' => 'nullable|numeric',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'integer|exists:branchs,id',
            'quantity_alert' => 'nullable|integer',
            'for_sale' => 'nullable|boolean',
            'min_sale' => 'nullable|numeric',
            'max_sale' => 'nullable|numeric',
            'image' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = null;

        DB::transaction(function () use ($validatedData, &$product) {
            $product = $this->ProductService->create($validatedData);
        });

        Notification::send(
            auth()->user(),
            new ProductNotification($product, 'create', 'تم إضافة المنتج ' . $product->name . ' بواسطة ' . auth()->user()->name)
        );

        if ($request->input('action') === 'add_and_open_stock') {
            return redirect()
                ->route('dashboard.products.openStock', $product->id)
                ->with('success', 'تمت إضافة المنتج وتم فتح المخزون.');
        }

        return redirect()
            ->back()
            ->with('success', 'تمت إضافة المنتج بنجاح!');
    }
    private function generateSkuFromName($name)
    {
        $existingSkus = Product::pluck('sku')->toArray();
        $sku = 1;
        while (in_array($sku, $existingSkus)) {
            $sku++;
        }
        return $sku;
    }
    public function edit($id)
    {
        $data = Product::with(['ProductUnitDetails.unit', 'Branches'])->findOrFail($id);
        $settings = Setting::first();
        $brands = Brand::all();
        $main_units = Unit::main()->get();
        $main_categories = Category::main()->get();
        $sub_categories = Category::where('parent_id', $data->category_id)->get();
        $Branches = Branch::active()->get();
        $salesSegments = SalesSegment::all();

        $sub_units = Unit::where(function ($query) use ($data) {
            $query->where("base_unit_id", $data->unit_id)
                ->orWhere("id", $data->unit_id);
        })->get();

        $sales_segment_prices = [];
        foreach ($salesSegments as $segment) {
            $sales_segment_prices[$segment->id] = $data->ProductUnitDetails->pluck('sale_price', 'unit_id');
        }

        return view('Dashboard.products.edit')->with([
            'data' => $data,
            'brands' => $brands,
            'main_units' => $main_units,
            'main_categories' => $main_categories,
            'sub_categories' => $sub_categories,
            'sub_units' => $sub_units,
            'Branches' => $Branches,
            'settings' => $settings,
            'salesSegments' => $salesSegments,
            'sales_segment_prices' => $sales_segment_prices,
        ]);
    }



    public function update(Request $request)
    {
        // return response($request);
        $validatedData = $request->validate([
            'product_id' => 'nullable|integer|exists:products,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'main_category_id' => 'nullable|integer|exists:categories,id',
            'category_id' => 'nullable|integer|exists:categories,id',
            'unit_id' => 'required|integer|exists:units,id',
            'sub_unit_ids' => 'nullable|array',
            'sub_unit_ids.*' => 'integer|exists:units,id',
            'units' => 'required|array',
            'units.*.sale_price' => 'required|numeric',
            'units.*.purchase_price' => 'required|numeric',
            'units.*.sales_segments' => 'nullable|array',
            'units.*.sales_segments.*' => 'nullable|numeric',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'integer|exists:branchs,id',
            'quantity_alert' => 'nullable|integer',
            'min_sale' => 'nullable|numeric',
            'max_sale' => 'nullable|numeric',
            'image' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);

        if (!isset($validatedData['product_id']) || !Product::find($validatedData['product_id'])) {
            throw new Exception('Product ID is invalid or missing');
        }

        $product = null;


        try {
            DB::transaction(function () use ($validatedData, &$product) {
                $product = $this->ProductService->edit($validatedData);
            });
            Notification::send(auth()->user(), new ProductNotification($product, 'update', 'تم تعديل المنتج ' . $product->name . ' بواسطة ' . auth()->user()->name));

            return redirect()
                ->back()
                ->with('success', 'تمت إضافة المنتج بنجاح!');
        } catch (\Exception $e) {
            Log::error('حدث خطأ أثناء تحديث المنتج: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء تحديث المنتج. حاول مرة أخرى لاحقًا.',
                'error' => $e->getMessage(), // يمكن إزالة الرسالة لأسباب أمنية
            ], 500);
        }
    }



    public function destroy($user_id)
    {
        $product = Product::findOrFail($user_id);
        $product->delete();
        Notification::send(auth()->user(), new ProductNotification($product, 'delete', 'تم حذف المنتج ' . $product->name . ' بواسطة ' . auth()->user()->name));
        $this->activityLogsService->insert([
            'subject' => $product,
            'title' => 'تم حذف المنتج',
            'description' => 'تم حذف المنتج ' . $product->name,
            'proccess_type' => 'products',
            'user_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', trans('admin.success'));
    }

    public function history($product_id, Request $request)
    {
        $product = Product::findOrFail($product_id);
        $branch_id = $request->branch_id ?? $product->Branches->first()->id;
        $statistics = $product->getProductStatistics($branch_id); // Pass branch_id here
        $branchesIds = ProductBranch::where('product_id', $product_id)->pluck('branch_id')->toArray();
        $branches = Branch::whereIn('id', $branchesIds)->get();
        $historyResult = $this->StockService->history($product, $branch_id);
        
        $data = $historyResult['data'];
        $quantity = $historyResult['quantity'];
        $statistics['quantity'] = $quantity;

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('type', function ($row) {
                    return trans('admin.' . $row['type']);
                })
                ->addColumn('created_by', function ($row) {
                    return $row['created_by'] ?? '-';
                })->rawColumns(['type','ref_no'])
                ->make(true);
        }

        return view('Dashboard.products.history', compact('product', 'product_id', 'branches', 'statistics', 'branch_id', 'quantity'));
    }

    public function getStatisticsByBranch(Product $product, $branch_id = null)
    {
        return response()->json($product->getProductStatistics($branch_id));
    }
    public function openStockView($product_id)
    {
        $product = Product::findOrFail($product_id);
        return view('Dashboard.products.open-stock')->with([
            'product' => $product
        ]);
    }

    public function openStock(Request $request, $product_id)
    {
        $product = Product::findOrFail($product_id);
        DB::beginTransaction();
        foreach ($request->open_stock as $branch_id => $item) {
            $this->PurchaseService->open_stock($item, $branch_id);
        }
        Notification::send(auth()->user(), new ProductNotification($product, 'openStock', 'تم فتح المخزن للمنتج ' . $product->name . ' بواسطة ' . auth()->user()->name));
        DB::commit();
        return redirect('dashboard/products')->with('success', 'success');
    }

    public function bulkEdit(Request $request)
    {
        
        $productIds = explode(',', $request->query('ids', ''));
        $products = Product::with('ProductUnitDetails')->whereIn('id', $productIds)->get();

        $brands = Brand::get();
        $settings = Setting::first();
        $main_units = Unit::main()->get();
        $main_categories = Category::main()->get();

        $Branches = Branch::active()->get();
        $salesSegments = SalesSegment::all();

        $sub_categories = Category::whereIn("parent_id", $products->pluck('main_category_id'))->get();
        $sub_units = Unit::whereIn('id', $products->pluck('unit_id'))->orWhereIn("base_unit_id", $products->pluck('unit_id'))->get();

        // Prepare segment prices for each product
        $sales_segment_prices = [];
        foreach ($products as $product) {
            $sales_segment_prices[$product->id] = [];
            foreach ($salesSegments as $segment) {
                $sales_segment_prices[$product->id][$segment->id] = $product->ProductUnitDetails->pluck('sale_price', 'unit_id');
            }
        }


        return view('Dashboard.products.bulk-edit')->with([
            'products' => $products,
            'brands' => $brands,
            'main_units' => $main_units,
            'main_categories' => $main_categories,
            'sub_categories' => $sub_categories,
            'sub_units' => $sub_units,
            'Branches' => $Branches,
            'settings' => $settings,
            'salesSegments' => $salesSegments,
            'sales_segment_prices' => $sales_segment_prices,
        ]);
    }

    public function bulkUpdate(Request $request)
    {
        // return response($request);
        $validatedData = $request->validate([
            'bulk_edit_ids' => 'required|array',
            'bulk_edit_ids.*' => 'integer|exists:products,id',
            'products' => 'required|array', // البيانات لكل منتج
            'products.*.name' => 'required|string|max:255',
            'products.*.sku' => 'nullable|string|max:255',
            'products.*.description' => 'nullable|string|max:255',
            'products.*.brand_id' => 'nullable|integer|exists:brands,id',
            'products.*.main_category_id' => 'nullable|integer|exists:categories,id',
            'products.*.category_id' => 'nullable|integer|exists:categories,id',
            'products.*.units' => 'required|array',
            'products.*.units.*.sale_price' => 'required|numeric',
            'products.*.units.*.purchase_price' => 'required|numeric',
            'products.*.branch_ids' => 'nullable|array',
            'products.*.branch_ids.*' => 'integer|exists:branchs,id',
            'products.*.quantity_alert' => 'nullable|integer',
            'products.*.min_sale' => 'nullable|numeric',
            'products.*.max_sale' => 'nullable|numeric',
            'image' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);

        // معالجة المنتجات داخل foreach
        foreach ($validatedData['bulk_edit_ids'] as $index => $productId) {
            DB::transaction(function () use ($validatedData, $index, $productId) {
                $product = Product::findOrFail($productId);
                $productData = $validatedData['products'][$productId];
                $updatedProduct = $this->ProductService->edit(array_merge($productData, ['product_id' => $productId]));
                Notification::send(
                    auth()->user(),
                    new ProductNotification(
                        $updatedProduct,
                        'update',
                        'تم تعديل المنتج ' . $updatedProduct->name . ' بواسطة ' . auth()->user()->name
                    )
                );
            });
        }

        return redirect('dashboard/products')->with('success', 'Products updated successfully');
    }






    public function ProudctsByBranch(Request $request)
    {
        $products = Product::where('for_sale', true)->HasStock(Request()->branch_id)->get();
        return view('Dashboard.products.product-select-box')->with([
            'products' => $products,
        ]);
    }
    public function ProudctsByBranchForTransfer(Request $request)
    {
        $products = Product::where('for_sale', true)
            ->whereHas('Branches', function ($query) use ($request) {
                $query->where('branch_id', $request->from_branch_id);
            })
            ->whereHas('Branches', function ($query) use ($request) {
                $query->where('branch_id', $request->to_branch_id);
            })
            ->HasStock($request->from_branch_id)
            ->get();

        return view('Dashboard.stock-transfer.product-select-box')->with([
            'products' => $products,
        ]);
    }
    public function importView()
    {

        return view('Dashboard.products.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel' => 'required|mimes:xlsx,xls',
        ]);

        try {
            DB::beginTransaction();
            Excel::import(new ProductsImport, $request->file('excel'));
            DB::commit();

            return redirect()
                ->route('dashboard.products.importView')
                ->with('success', trans('admin.products_imported_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    public function downloadTemplateExcel()
    {

        $filePath = public_path('files/products/templateExcelImport.xlsx');


        if (!file_exists($filePath)) {
            return redirect()->back()->withErrors(['templateExcelImport' => 'القالب غير موجود']);;
        }


        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="templateExcelImport.xlsx"',
        ];

        // Return the download response
        return response()->download($filePath, 'templateExcelImport.xlsx', $headers);
    }

    public function branches($product_id)
    {
        $product = Product::with('Branches')->findOrFail($product_id);
        return view('Dashboard.products.branches_modal', compact('product'));
    }
    public function show($id)
    {
        $settings = Setting::first();
        $product = Product::findOrFail($id);
        return [
            'title' => trans('admin.Show') . ' ' . $product->name,
            'body' => view('Dashboard.products.show', with([
                'product' => $product,
                'settings' => $settings
            ]))->render(),
        ];
    }
    public function search(Request $request)
    {
        $searchTerm = $request->input('query');
        $products = Product::where('name', 'LIKE', "%{$searchTerm}%")->get();
        return response()->json($products);
    }
    public function importOpenStockView()
    {
        return view('Dashboard.products.import-open-stock');
    }
    public function importOpenStock(Request $request)
    {
        $request->validate([
            'excel-open-stock' => 'required|file|mimes:xlsx,xls'
        ]);

        $excel = $request->file('excel-open-stock');
        $data = Excel::toArray([], $excel)[0];

        foreach ($data as $row) {
            $product = Product::where('sku', 'LIKE', '%' . $row[0] . '%')->first();
            if (!$product) {
                continue;
            }

            // Trim whitespace from the branch name in the Excel row
            $branchName = trim($row[1]);
            $branch = Branch::where('name', $branchName)->first();
            if (!$branch) {
                continue;
            }

            $branch_id = $branch->id;
            $product_id = $product->id;
            Log::info('Product ID: ' . $product_id);
            Log::info('Branch ID: ' . $branch_id);

            $productBranch = ProductBranch::where('product_id', $product_id)
                ->where('branch_id', $branch_id)
                ->first();

            if ($productBranch) {
                $productBranchDetails = ProductBranchDetails::where([
                    ['branch_id', $branch_id],
                    ['product_id', $product_id]
                ])->first();
                if ($productBranchDetails) {
                    $productBranchDetails->update([
                        'qty_available' => $row[2]
                    ]);
                } else {
                    ProductBranchDetails::create([
                        'branch_id' => $branch_id,
                        'product_id' => $product_id,
                        'qty_available' => $row[2]
                    ]);
                }
            } else {
                return redirect()->route('dashboard.products.importOpenStockView')->with('error', 'المنتج موجود بالفعل في هذا الفرع');
            }
        }

        return redirect()->route('dashboard.products.index')
            ->with('success', trans('admin.open_stock_imported_successfully'));
    }
}
