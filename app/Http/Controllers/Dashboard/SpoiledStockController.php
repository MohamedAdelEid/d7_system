<?php

namespace App\Http\Controllers\Dashboard;

use Exception;
use Carbon\Carbon;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ProductBranchDetails;
use App\Services\TransactionService;
use App\Services\ActivityLogsService;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SpoiledStockNotification;

class SpoiledStockController extends Controller
{
    protected $transactionService;
    protected $StockService;
    protected $activityLogService;
    public function __construct(TransactionService $transactionService, StockService $StockService, ActivityLogsService $activityLogService)
    {
        $this->transactionService = $transactionService;
        $this->StockService = $StockService;
        $this->activityLogService = $activityLogService;
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Transaction::with('spoiledLines')->where('type', 'spoiled_stock')->orderBy('id', 'desc');
            return DataTables::of($data)
                ->addColumn('branch', function ($row) {
                    return $row->branch?->name;
                })
                ->addColumn('status', function ($row) {
                    return $row->status;
                })
                ->addColumn('transaction_date', function ($row) {
                    return Carbon::parse($row->transaction_date)->format('Y-m-d');
                })
                ->addColumn('action', function ($row) {
                    $btn =  '<div class="btn-group"><button type="button" class="btn btn-success">'. trans('admin.Actions') .'</button><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"></button><div class="dropdown-menu" role="menu">';
                    if(auth('user')->user()->has_permission('read-spoiled-stock')){
                        $btn .= '<a class="dropdown-item fire-popup" data-url="' . route('dashboard.spoiled-stock.show', $row->id) . '"
                                href="#" data-toggle="modal" data-target="#modal-default-big">' . trans("admin.Show") . '</a>';
                    }
                    if(auth('user')->user()->has_permission('update-spoiled-stock')){
                        $btn .= '<a class="dropdown-item" href="' . route('dashboard.spoiled-stock.edit', $row->id).'">' . trans("admin.Edit") . '</a>';
                    }
                    if(auth('user')->user()->has_permission('delete-spoiled-stock')){
                        $btn .= '<a class="dropdown-item delete-popup" href="#" data-toggle="modal" data-target="#modal-default" data-url="'.route("dashboard.spoiled-stock.destroy", $row->id).'">' . trans('admin.Delete') . '</a>';
                    }
                    if(auth('user')->user()->has_permission('change-spoiled-stock-status')){
                        $btn .= '<a class="dropdown-item fire-popup" data-url="' . route('dashboard.spoiled-stock.changeStatusView', $row->id) . 
                        '" href="#" data-toggle="modal" data-target="#modal-default">' . trans('admin.Transfer Status') . '</a>';
                    }
                    $btn.= '</div></div>';
                    return $btn;
                })
                ->addColumn('route', function ($row) {
                    return route('dashboard.spoiled-stock.show', $row->id);
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('Dashboard.spoiled-stock.index');
    }
    public function create()
    {
        $branches = Branch::all();
        $products = [];
        return view('Dashboard.spoiled-stock.create', compact('branches', 'products'));
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'branch_id' => 'required|integer|exists:branchs,id',
            'products' => 'required|array',
            'products.*.id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_id' => 'required|integer',
            'status'           => 'required|in:pending,final'
        ]);

        $transactionData = [
            'branch_id' => $validatedData['branch_id'],
            'type' => 'spoiled_stock',
            'status' =>  $validatedData['status'],
            'transaction_date' => now(),
        ];
        $transaction = $this->transactionService->CreateTransaction($transactionData);
        $spoiledLines = [];
        foreach ($validatedData['products'] as $product) {
            $productModel = Product::find($product['id']);
            $mainQuantity = $this->StockService->getMainUnitQuantityFromSubUnit($productModel, $product['unit_id'], $product['quantity']);
            $spoiledLines[] = [
                'transaction_id' => $transaction->id,
                'product_id' => $product['id'],
                'quantity' => $product['quantity'],
                'unit_id' => $product['unit_id'] ?? null,
                'main_unit_quantity'    => $mainQuantity,
            ];
            if ($validatedData['status'] == 'final') {
                $this->StockService->SubtractFromStock(
                    $product['id'],
                    $transaction->branch_id,
                    $mainQuantity
                );
            }
        }
        $spoiledLines = $this->transactionService->CreateSpoiledLines($transaction, $spoiledLines);
        Notification::send(auth()->user(), new SpoiledStockNotification($transaction, $spoiledLines, 'create', 'تم اضافة مخزون تالف جديد في الفرع ' . $transaction->Branch->name . ' بتاريخ ' . Carbon::parse($transaction->transaction_date)->format('Y-m-d') . ' بواسطة ' . auth()->user()->name));
        $description = 'تم إضافة مخزون تالف جديد من المنتجات :' . PHP_EOL;
        
        foreach ($spoiledLines->SpoiledLines as $line) {
            $description .= $line->quantity . ' من ' . $line->product->name . PHP_EOL;
        }
        
        $description .= 'لصالح فرع ' . $transaction->branch->name . '.';
            
        $this->activityLogService->insert([
            'subject' => $transaction,
            'title' => 'تم إضافة مخزون تالف جديد',
            'description' => $description,
            'proccess_type' => 'spoiled_stock',
            'user_id' => auth()->id(),
        ]);
        return redirect()->route('dashboard.spoiled-stock.index')->with('success', trans('admin.spoiled_stock_created_successfully'));
    }
    public function ProductRowAdd(Request $request)
    {
        $product = Product::find($request->product_id);
        $product_row = $this->product_row($product, $request->branch_id);
        return view('Dashboard.spoiled-stock.parts.product_raw', compact('product_row'));
    }
    private function product_row($product, $branch_id)
    {
        $available_quantity = 0;
        $product_branch_details = $product->ProductBranchDetails()->where('branch_id', $branch_id)->first();
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
    public function show($id)
    {
        $spoiledStock = Transaction::with('SpoiledLines')->find($id);
        return [
            'title' => trans('admin.Show') . ' ' . trans('admin.spoiled_stock'),
            'body'  => view('Dashboard.spoiled-stock.show')->with([
                'spoiledStock' => $spoiledStock,
            ])->render(),
        ];
    }
    public function edit($id)
    {
        $spoiledStock = Transaction::with('spoiledLines.product')->find($id);
        $branches = Branch::all();
        $products = Product::whereHas('productBranchDetails', function ($query) use ($spoiledStock) {
            $query->where('branch_id', $spoiledStock->branch_id)
                ->where('qty_available', '>', 0);
        })->get();
        return view('Dashboard.spoiled-stock.edit', compact('spoiledStock', 'branches', 'products'));
    }
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'branch_id' => 'required|exists:branchs,id',
            'products' => 'required|array',
            'products.*.id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer',
            'products.*.unit_id' => 'required|integer',
        ]);
        $spoiledStock = Transaction::with('spoiledLines.product')->findOrFail($id);
        if ($spoiledStock->branch_id != $validatedData['branch_id']) {
            $spoiledStock->update(['branch_id' => $validatedData['branch_id']]);
        }



        foreach ($validatedData['products'] as $productId => $productData) {
            $spoiledLine = $spoiledStock->spoiledLines()->where('product_id', $productId)->first();
            $product = Product::findOrFail($productId);
            $availableQuantity = $product->productBranchDetails()
                ->where('branch_id', $validatedData['branch_id'])
                ->first()->qty_available ?? 0;

            $mainQuantity = $this->StockService->getMainUnitQuantityFromSubUnit($product, $productData['unit_id'], $productData['quantity']);
            if ($spoiledLine) {

                $spoiledLine->update(['quantity' => $productData['quantity'], 'main_unit_quantity' => $mainQuantity]);
            } else {
                $spoiledStock->spoiledLines()->create([
                    'product_id' => $productId,
                    'quantity' => $productData['quantity'],
                    'main_unit_quantity' => $mainQuantity,
                ]);
            }
        }
        $spoiledStock->spoiledLines()
            ->whereNotIn('product_id', array_keys($validatedData['products']))
            ->get()
            ->each(function ($spoiledLine) use ($validatedData) {
                $this->StockService->addToStock($spoiledLine->product?->id, $validatedData['branch_id'], $spoiledLine->quantity);
                $spoiledLine->delete();
            });
        Notification::send(auth()->user(), new SpoiledStockNotification($spoiledStock, $spoiledStock, 'update', 'تم تحديث عملية مخزون تالف في الفرع ' . $spoiledStock->Branch->name . ' بتاريخ ' . Carbon::parse($spoiledStock->transaction_date)->format('Y-m-d') . ' بواسطة ' . auth()->user()->name));
        $description = 'تم تحديث مخزون تالف من المنتجات :' . PHP_EOL;

        foreach ($validatedData['products'] as $productId => $productData) {
                $product = Product::find($productId);
                $description .= $productData['quantity'] . ' من ' . $product->name . PHP_EOL;
            }
            
        $description .= 'لصالح فرع ' . $spoiledStock->branch->name . '.';
        $this->activityLogService->insert([
            'subject' => $spoiledStock,
            'title' => 'تم تحديث مخزون تالف',
            'description' => $description,
            'proccess_type' => 'spoiled_stock',
            'user_id' => auth()->id(),
        ]);
        return redirect()->route('dashboard.spoiled-stock.index')->with('success', trans('admin.spoiled_stock_updated_successfully'));
    }

    public function destroy($id)
    {
        $spoiledStock = Transaction::find($id);
        $spoiledLines = $spoiledStock->spoiledLines;

        if ($spoiledStock->status == 'final') {
            $this->StockService->bulckAddToStockBySpoiledLinesLines($spoiledStock, $spoiledStock->SpoiledLines);
        }
        $spoiledStock->delete();
        Notification::send(auth()->user(), new SpoiledStockNotification($spoiledStock, $spoiledStock, 'delete', 'تم حذف عملية مخزون تالف في الفرع ' . $spoiledStock->Branch->name . ' بتاريخ ' . Carbon::parse($spoiledStock->transaction_date)->format('Y-m-d') . ' بواسطة ' . auth()->user()->name));
        $description = 'تم حذف مخزون تالف : ' . PHP_EOL;

        foreach ($spoiledLines as $spoiledLine) {
            $description .= $spoiledLine->quantity . ' من ' . $spoiledLine->product->name . PHP_EOL;
        }
        
        $description .= 'لصالح فرع ' . $spoiledStock->branch->name . '.';
        $this->activityLogService->insert([
            'subject' => $spoiledStock,
            'title' => 'تم حذف مخزون تالف',
            'description' => $description,
            'proccess_type' => 'spoiled_stock',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('dashboard.spoiled-stock.index')->with('success', trans('admin.spoiled_stock_deleted_successfully'));
    }

    public function changeStatusView($transaction_id)
    {
        $transaction = Transaction::find($transaction_id);
        return [
            'title' => trans('admin.Show'),
            'body'  => view('Dashboard.spoiled-stock.change-status')->with([
                'transaction' => $transaction,
            ])->render(),
        ];
    }
    public function changeStatus($transaction_id, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,final',
        ]);

        $transaction = Transaction::with('SpoiledLines')->findOrFail($transaction_id);

        if ($transaction->status == $request->status) {

            return redirect()->back()->with('error', 'يجب تغير الحالة');
        }
        try {

            DB::beginTransaction();

            $transaction->update(['status' => $request->status]);


            if ($request->status == 'pending') {
                $this->StockService->bulckAddToStockBySpoiledLinesLines($transaction, $transaction->SpoiledLines);
            }
            if ($request->status == 'final') {
                $this->StockService->bulckSubtractFromStockBySpoiledLinesLines($transaction, $transaction->SpoiledLines);
            }

            DB::commit();
            Notification::send(auth()->user(), new SpoiledStockNotification($transaction, $transaction, 'changeStatus', 'تم تغير حالة مخزون تالف إلى ' . $request->status . ' في الفرع ' . $transaction->Branch->name . ' بتاريخ ' . Carbon::parse($transaction->transaction_date)->format('Y-m-d') . ' بواسطة ' . auth()->user()->name));
            $description = 'تم تغير حالة مخزون تالف من ' . $transaction->status . ' إلى ' . $request->status;
            $this->activityLogService->insert([
                'subject' => $transaction,
                'title' => 'تم تغير حالة مخزون تالف',
                'description' => 'تم تغير حالة مخزون تالف من ' . $transaction->status . ' إلى ' . $request->status,
                'proccess_type' => 'spoiled_stock',
                'user_id' => auth()->id(),
            ]);
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
        return redirect()->route('dashboard.spoiled-stock.index')->with('success', 'تم تغير الحالة بنجاح');
    }
}
