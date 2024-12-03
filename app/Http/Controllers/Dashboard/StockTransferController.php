<?php

namespace App\Http\Controllers\Dashboard;

use Exception;
use Carbon\Carbon;
use App\Models\Unit;
use App\Models\Brand;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransferLine;
use Illuminate\Http\Request;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ProductBranchDetails;
use App\Services\TransactionService;
use App\Services\ActivityLogsService;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StockTransferNotification;

class StockTransferController extends Controller
{
    protected $StockService;
    protected $activityLogService;
    public function __construct(private TransactionService $transactionService, StockService $StockService, ActivityLogsService $activityLogService)
    {
        $this->StockService = $StockService;
        $this->activityLogService = $activityLogService;
    }
    public function index(Request $request)
    {
        // $stock_transfers = Transaction::with('Branch', 'branchTo', 'TransferLines')->where('type', 'transfer')->get();
        if ($request->ajax()) {
            $data = Transaction::with('Branch', 'branchTo', 'TransferLines')->orderBy('id', 'desc')->where('type', 'transfer');
            return DataTables::of($data)
                ->addColumn('from_branch', function ($row) {
                    return $row->Branch?->name;
                })
                ->addColumn('to_branch', function ($row) {
                    return $row->branchTo?->name;
                })
                ->addColumn('transaction_date', function ($row) {
                    return Carbon::parse($row->transaction_date)->format('Y-m-d');
                })
                ->addColumn('status', function ($row) {
                    return $row->status;
                })
                ->addColumn('action', function ($row) {
                    $btn =  '<div class="btn-group"><button type="button" class="btn btn-success">'. trans('admin.Actions') .'</button><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"></button><div class="dropdown-menu" role="menu">';
                    if(auth('user')->user()->has_permission('read-stock-transfers')){
                        $btn .= '<a class="dropdown-item fire-popup" data-url="' . route('dashboard.stock-transfers.show', $row->id) . '"
                                href="#" data-toggle="modal" data-target="#modal-default-big">' . trans("admin.Show") . '</a>';
                    }
                    if(auth('user')->user()->has_permission('update-stock-transfers')){
                        $btn .= '<a class="dropdown-item" href="' . route('dashboard.stock-transfers.edit', $row->id).'">' . trans("admin.Edit") . '</a>';
                    }
                    if(auth('user')->user()->has_permission('delete-stock-transfers')){
                        $btn .= '<a class="dropdown-item delete-popup" href="#" data-toggle="modal" data-target="#modal-default" data-url="'.route("dashboard.stock-transfers.destroy", $row->id).'">' . trans('admin.Delete') . '</a>';
                    }
                    if(auth('user')->user()->has_permission('change-stock-transfer-status')){
                        $btn .= '<a class="dropdown-item fire-popup" data-url="' . route('dashboard.stock-transfers.changeStatusView', $row->id) . 
                        '" href="#" data-toggle="modal" data-target="#modal-default">' . trans('admin.Transfer Status') . '</a>';
                    }
                    $btn.= '</div></div>';
                    return $btn;
                })
                ->addColumn('route', function ($row) {
                    return route('dashboard.stock-transfers.show', $row->id);
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('Dashboard.stock-transfer.index');
    }
    public function create()
    {
        $branches = Branch::all();
        $brands = Brand::all();
        $products = [];
        return view('Dashboard.stock-transfer.create', compact('branches', 'products','brands'));
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'from_branch_id' => 'required|integer|exists:branchs,id',
            'to_branch_id' => 'required|integer|exists:branchs,id|different:from_branch_id',
            'status' => 'required|in:pending,final',
            'products' => 'required|array',
            'products.*.id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_id' => 'required|integer|min:1',
        ]);
        $transactionData = [
            'branch_id' => $validatedData['from_branch_id'],
            'branch_to_id' => $validatedData['to_branch_id'],
            'type' => 'transfer',
            'status' => $validatedData['status'],
            'transaction_date' => now(),
        ];

        $transaction = $this->transactionService->CreateTransaction($transactionData);

        $transferLines = [];
        foreach ($validatedData['products'] as $product) {
            $productModel = Product::find($product['id']);
            $mainQuantity = $this->StockService->getMainUnitQuantityFromSubUnit($productModel, $product['unit_id'], $product['quantity']);
            $transferLines[] = [
                'product_id' => $product['id'],
                'quantity' => $product['quantity'],
                'unit_id' => $product['unit_id'] ?? null,
                'main_unit_quantity' => $mainQuantity,
            ];

            $this->StockService->SubtractFromStock($product['id'], $validatedData['from_branch_id'], $mainQuantity);

            if ($validatedData['status'] == "final") {

                $this->StockService->addToStock($product['id'], $validatedData['to_branch_id'], $mainQuantity);
            }
        }
        $this->transactionService->CreateTransferLines($transaction, $transferLines);
        Notification::send(auth()->user(), new StockTransferNotification($transaction, $transaction, 'create', "تم نقل المخزون بنجاح من الفرع {$transaction->Branch->name} إلى الفرع {$transaction->branchTo->name} بتاريخ " . Carbon::parse($transaction->transaction_date)->format('Y-m-d') . " بواسطة " . auth()->user()->name));
        $from_branch = Branch::find($validatedData['from_branch_id']);
        $to_branch = Branch::find($validatedData['to_branch_id']);
        // Log the activity
        $this->activityLogService->insert([
            'subject' => $transaction,
            'title' => 'تم إضافة نقل مخزون جديد',
            'description' => 'تم إضافة نقل مخزون من فرع ' . $from_branch->name . ' إلى فرع ' . $to_branch->name . ' بكمية ' . array_sum(array_column($validatedData['products'], 'quantity')) . ' منتج.',
            'proccess_type' => 'stock_transfer',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('dashboard.stock-transfers.index')->with('success', trans('admin.stock_transfer_created_successfully'));
    }
    public function edit($id)
    {
        $stock_transfer = Transaction::with('Branch', 'branchTo', 'TransferLines')->findOrFail($id); // Load TransferLines with product and unit
        $branches = Branch::all();
        $brands = Brand::all();
        $products = Product::whereHas('productBranchDetails', function ($query) use ($stock_transfer) {
            $query->where('branch_id', $stock_transfer->branch_id)
                ->where('qty_available', '>', 0);
        })->get();
    
        $from_branch_id = $stock_transfer->branch_id;
        $to_branch_id = $stock_transfer->branch_to_id;
    
        
        // Map TransferLines to extract product information for existing products
        $existingProducts = $stock_transfer->TransferLines->map(function ($line) use ($stock_transfer) {
            $unit = Unit::find($line->unit_id);  // Find the unit for the current line
            $product = $line->Product;           // Access the product from the TransferLine
        
            // Check if the selected unit is the main unit
            if ($line->unit_id == $product->MainUnit->id) {
                // Use the stock as-is for the main unit
                $availableQuantity = $product->getStockByBranch($stock_transfer->branch_id);
            } else {
                // Adjust stock for non-main units based on the unit multiplier
                $availableQuantity = $product->getStockByBranch($stock_transfer->branch_id) / $unit->base_unit_multiplier;
            }
        
            return [
                'id' => $product->id,
                'name' => $product->name,
                'units' => $product->GetAllUnits(),
                'available_quantity' => $availableQuantity, // Assuming the method is defined on the Product model
                'quantity' => $line->quantity, // Access the quantity from the TransferLine
                'unit' => $product->MainUnit->id, // Get the actual unit name
            ];
        });
    
        return view('Dashboard.stock-transfer.edit', compact('stock_transfer', 'brands', 'branches', 'products', 'from_branch_id', 'to_branch_id', 'existingProducts'));
    }
    
 
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'from_branch_id' => 'required|integer|exists:branchs,id',
            'to_branch_id' => 'required|integer|exists:branchs,id|different:from_branch_id',
            'products' => 'required|array',
            'products.*.id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);
        $stock_transfer = Transaction::with('TransferLines.product')->findOrFail($id);

        DB::beginTransaction();

        try {
            if ($stock_transfer->branch_id !=  $validatedData['from_branch_id']) {
                $stock_transfer->update(['branch_id' => $validatedData['from_branch_id']]);
            }
            if ($stock_transfer->branch_to_id !=  $validatedData['to_branch_id']) {
                $stock_transfer->update(['branch_to_id' => $validatedData['to_branch_id']]);
            }

            // Create new transfer lines
            foreach ($validatedData['products'] as $productId => $productData) {
                $transferLine = $stock_transfer->TransferLines()->where('product_id', $productId)->first();
                $product = Product::findOrFail($productId);
                $availableQuantity = $product->productBranchDetails()
                    ->where('branch_id', $validatedData['from_branch_id'])
                    ->first()->qty_available ?? 0;

                $old_quantity = $transferLine->quantity;
                $new_quantity = $productData['quantity'];
                $new_mainQuantity = $this->StockService->getMainUnitQuantityFromSubUnit($product, $transferLine['unit_id'], $new_quantity);
                $old_mainQuantity = $this->StockService->getMainUnitQuantityFromSubUnit($product, $transferLine['unit_id'], $new_quantity);
                if ($transferLine) {
                    $quantityDifference = $new_quantity - $old_quantity;
                    $Difference_mainQuantity = $this->StockService->getMainUnitQuantityFromSubUnit($product, $transferLine['unit_id'], $quantityDifference);
                    $transferLine->update(['quantity' => $new_quantity, 'main_unit_quantity' => $new_mainQuantity]);
                    $this->StockService->SubtractFromStock($product['id'], $validatedData['from_branch_id'], $Difference_mainQuantity);
                    $this->StockService->addToStock($product['id'], $validatedData['to_branch_id'], $Difference_mainQuantity);
                } else {
                    $stock_transfer->TransferLines()->create([
                        'product_id' => $productId,
                        'quantity' => $new_quantity,
                        'main_unit_quantity' => $new_mainQuantity,
                    ]);
                    $this->StockService->SubtractFromStock($product['id'], $validatedData['from_branch_id'], $new_mainQuantity);
                    $this->StockService->addToStock($product['id'], $validatedData['to_branch_id'], $new_mainQuantity);
                }

                $stock_transfer->TransferLines()
                    ->whereNotIn('product_id', array_keys($validatedData['products']))
                    ->get()
                    ->each(function ($transferLine) use ($validatedData, $old_mainQuantity) {
                        $this->StockService->addToStock($transferLine->product?->id, $validatedData['from_branch_id'], $old_mainQuantity);
                        $this->StockService->SubtractFromStock($transferLine->product?->id, $validatedData['to_branch_id'], $old_mainQuantity);
                        $transferLine->delete();
                    });
            }

            DB::commit();
            Notification::send(auth()->user(), new StockTransferNotification($stock_transfer, $stock_transfer, 'update', "تم تحديث نقل مخزون بنجاح من الفرع {$stock_transfer->Branch->name} إلى الفرع {$stock_transfer->branchTo->name} بتاريخ " . Carbon::parse($stock_transfer->transaction_date)->format('Y-m-d') . " بواسطة " . auth()->user()->name));
            $from_branch = Branch::find($validatedData['from_branch_id']);
            $to_branch = Branch::find($validatedData['to_branch_id']);
            // Log the activity
            $this->activityLogService->insert([
                'subject' => $stock_transfer,
                'title' => 'تم تحديث نقل مخزون',
                'description' => 'تم تحديث نقل مخزون من فرع ' . $from_branch->name . ' إلى فرع ' . $to_branch->name . ' بكمية ' . array_sum(array_column($validatedData['products'], 'quantity')) . ' منتج.',
                'proccess_type' => 'stock_transfer',
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('dashboard.stock-transfers.index')->with('success', trans('admin.stock_transfer_updated_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating stock transfer: ' . $e->getMessage());
            return redirect()->route('dashboard.stock-transfers.index')->with('error', trans('admin.error_updating_stock_transfer'));
        }
    }
    public function destroy($id)
    {
        try {
            $stock_transfer = Transaction::findOrFail($id);
            $from_branch = Branch::find($stock_transfer->branch_id);
            $to_branch = Branch::find($stock_transfer->branch_to_id);
        
            DB::beginTransaction();
        
            $total_quantity = 0; // متغير لحساب مجموع الكميات
        
            foreach ($stock_transfer->TransferLines as $line) {
                $this->StockService->addToStock($line->product_id, $stock_transfer->branch_id, $line->main_unit_quantity);
        
                if ($stock_transfer->status == 'final') {
                    $this->StockService->SubtractFromStock($line->product_id, $stock_transfer->branch_to_id, $line->main_unit_quantity);
                }
        
                // اجمع الكمية
                $total_quantity += $line->main_unit_quantity;
            }
        
            $stock_transfer->TransferLines()->delete();
            $stock_transfer->delete();
        
            DB::commit();
            Notification::send(auth()->user(), new StockTransferNotification($stock_transfer, $stock_transfer, 'delete', "تم حذف نقل مخزون بنجاح من الفرع {$stock_transfer->Branch->name} إلى الفرع {$stock_transfer->branchTo->name} بتاريخ " . Carbon::parse($stock_transfer->transaction_date)->format('Y-m-d') . " بواسطة " . auth()->user()->name));
            // ادراج سجل في جدول النشاط
            $this->activityLogService->insert([
                'subject' => $stock_transfer,
                'title' => 'تم حذف نقل مخزون',
                'description' => 'تم حذف نقل مخزون من فرع ' . $from_branch->name . ' إلى فرع ' . $to_branch->name . ' بكمية ' . $total_quantity . ' منتج.',
                'proccess_type' => 'stock_transfer',
                'user_id' => auth()->id(),
            ]);
            
            return redirect()->route('dashboard.stock-transfers.index')->with('success', trans('admin.stock_transfer_deleted_successfully'));
        } catch (\Exception $e) {
            \Log::error('Error deleting stock transfer: ' . $e->getMessage());
            return redirect()->route('dashboard.stock-transfers.index')->with('error', trans('admin.error_deleting_stock_transfer'));
        }
        
    }

    public function show($id)
    {
        $stock_transfer = Transaction::find($id);
        return [
            'title' => trans('admin.Show') . ' ' . trans('admin.stock_transfer'),
            'body'  => view('Dashboard.stock-transfer.show')->with([
                'stock_transfer' => $stock_transfer,
            ])->render(),
        ];
    }
    public function ProductRowAdd(Request $request)
    {
        $product = Product::find($request->product_id);
        $product_row = $this->product_row($product, $request->from_branch_id);
        return view('Dashboard.stock-transfer.parts.product_raw', compact('product_row'));
    }
    private function product_row($product, $from_branch_id)
    {
        $available_quantity = 0;
        $product_branch_details = $product->ProductBranchDetails()->where('branch_id', $from_branch_id)->first();
        if ($product_branch_details) {
            $available_quantity = $product_branch_details->qty_available;
        }

        return [
            'id' => $product->id,
            'name' => $product->name,
            'units' => $product->GetAllUnits(),
            'quantity' => $available_quantity,
            'available_quantity' => $available_quantity,
        ];
    }

    public function changeStatusView($transaction_id)
    {
        $transaction = Transaction::find($transaction_id);
        return [
            'title' => trans('admin.Show'),
            'body'  => view('Dashboard.stock-transfer.change-status')->with([
                'transaction' => $transaction,
            ])->render(),
        ];
    }

    public function changeStatus($transaction_id, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,final',
        ]);

        $transaction = Transaction::with('TransferLines')->findOrFail($transaction_id);

        $branchFrom = Branch::findOrFail($transaction->branch_id);
        $branchTo = Branch::findOrFail($transaction->branch_to_id);

        if ($transaction->status == $request->status) {

            return redirect()->back()->with('error', 'يجب تغير الحالة');
        }
        try {

            DB::beginTransaction();

            $transaction->update(['status' => $request->status]);

            foreach ($transaction->TransferLines as  $line) {

           
                $product = Product::findOrFail($line->product_id);



                if ($request->status == 'pending') {

                    $this->StockService->SubtractFromStock($line->product_id, $branchTo->id , $line->quantity, $line->unit_id);
                
                }
                
                if ($request->status == 'final') {
                    $this->StockService->AddToStock($line->product_id, $branchTo->id, $line->quantity,  $line->unit_id);
                }

                DB::commit();
            }
            Notification::send(auth()->user(), new StockTransferNotification($transaction, $transaction, 'changeStatus', 'تم تغير حالة نقل مخزون إلى ' . $request->status .'بتاريخ ' . Carbon::parse($transaction->transaction_date)->format('Y-m-d') . ' بواسطة ' . auth()->user()->name));
            $this->activityLogService->insert([
                'subject' => $transaction,
                'title' => 'تم تغير حالة نقل مخزون',
                'description' => 'تم تغير حالة نقل مخزون من ' . $transaction->status . ' إلى ' . $request->status,
                'proccess_type' => 'stock_transfer',
                'user_id' => auth()->id(),
            ]);
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
        return redirect()->route('dashboard.stock-transfers.index')->with('success', 'تم تغير الحالة بنجاح');
    }
}
