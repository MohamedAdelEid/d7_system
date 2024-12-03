<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use App\Services\ActivityLogsService;
use App\Models\TransactionPurchaseLine;
use App\Services\PurchaseReturnService;
use Yajra\DataTables\Facades\DataTables;
use App\Notifications\PurchaseNotification;
use Illuminate\Support\Facades\Notification;

class PurchaseReturnController extends Controller
{
    public $PurchaseReturnService;
    public $TransactionService;
    protected $ActivityLogsService;
    public function __construct(PurchaseReturnService $PurchaseReturnService, ActivityLogsService $activityLogsService ,
                                TransactionService $TransactionService){
        $this->PurchaseReturnService = $PurchaseReturnService;
        $this->TransactionService = $TransactionService;
        $this->ActivityLogsService = $activityLogsService;
    }

    public function index(Request $request){
        $settings = Setting::first();
        if ($request->ajax()) {
            $data = Transaction::where('type', 'purchase_return')
                                ->orderBy('id', 'desc');

            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn =  '<div class="btn-group"><button type="button" class="btn btn-success">'. trans('admin.Actions') .'</button><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"></button><div class="dropdown-menu" role="menu">';
                        if (auth('user')->user()->has_permission('read')) 
                            $btn .= '<a class="dropdown-item fire-popup" data-url="'.route('dashboard.purchases.purchase-return.show', $row->id).'" 
                        href="#" data-toggle="modal" data-target="#modal-default-big">' . trans("admin.Show") . '</a>';

                        if (auth('user')->user()->has_permission('delete-purchase-return') && $row->TransactionFromReturnTransaction)
                            $btn .= '<a class="dropdown-item delete-popup" href="#" data-toggle="modal" data-target="#modal-default" data-url="'.route("dashboard.purchases.purchase-return.delete", $row->id).'">' . trans('admin.Delete') . '</a>';
                        if (auth('user')->user()->has_permission('print-purchase-return') && $row->TransactionFromReturnTransaction)
              
                        if ($settings->classic_printing) {
                            $btn .= '<a class="dropdown-item print-invoice"  href="' . route('dashboard.purchases.purchase-return.printInvoicePage', $row->id) . '">' . trans('admin.Classic Printing') . '</a>';
                        } else {
                                $btn .= '<a class="dropdown-item print-invoice"  href="' . route('dashboard.purchases.purchase-return.printInvoicePage', $row->id) . '">' . trans('admin.Thermal Printing') . '</a>';
                            }
                        $btn.= '</div></div>';
                        return $btn;
                    })
                    ->addColumn('contact', function($row){
                        return $row->Contact?->name;
                    })
                    ->addColumn('phone', function($row){
                        return $row->Contact?->phone;
                    })
                    ->addColumn('government', function($row){
                        return $row->Contact?->government;
                    })
                    ->addColumn('city', function($row){
                        return $row->Contact?->city;
                    })
                    ->addColumn('ref_no', function($row){
                        return $row->ref_no;
                    })
                    ->addColumn('route', function($row){
                        return route('dashboard.purchases.purchase-return.show', $row->id);
                    })
                    ->addColumn('total', function($row){
                        return number_format($row->final_price,1);
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('Dashboard.purchase-return.index', compact('settings'));
    }

    
    public function create($purchase_id){
        $transaction = Transaction::find($purchase_id);
        return [
            'title' => trans('admin.purchase-return'),
            'body'  => view('Dashboard.purchase-return.create')->with([
                'transaction'   => $transaction
            ])->render(),
        ];
    }

    public function store(Request $request)
{
    // Retrieve all request data
    $data = $request->all();

    // Fetch quantities for validation
    $productsReturn = $request->products_return;

    $validationErrors = [];
    foreach ($productsReturn as $lineId => $item) {
        // Use Eloquent to fetch the purchase line with the related product
        $purchaseLine = TransactionPurchaseLine::with('Product')
            ->find($lineId);
    
        if (!$purchaseLine) {
            $validationErrors[] = "Invalid transaction line ID: {$lineId}";
        } elseif ($item['return_quantity'] > $purchaseLine->quantity) {
            $validationErrors[] = "كمية المرتجع لمنتج {$purchaseLine->Product->name} يجب أن تكون أقل من أو تساوي الكمية المتاحة ({$purchaseLine->quantity}).";
        }
    }

    // If there are validation errors, return back with errors
    if (!empty($validationErrors)) {
        return redirect('dashboard/purchases')->withErrors($validationErrors)->withInput();
    }

    DB::beginTransaction();

    try {
        // Fetch the purchase transaction
        $purchaseSell = Transaction::findOrFail($request->transaction_id);

        $data = [
            'branch_id'  => $purchaseSell->branch_id,
            'contact_id' => $purchaseSell->contact_id,
            'status'     => "final",
            'payment_type' => $purchaseSell->payment_type,
        ];

        // Prepare return lines
        $returnLinesArray = [];
        foreach ($productsReturn as $item) {
            $returnLinesArray[] = [
                'product_id'                    => $item['product_id'],
                'quantity'                      => $item['return_quantity'],
                'unit_price'                    => $item['unit_price'],
                'unit_id'                       => $item['unit_id'],
                'transactions_purchase_line_id' => $item['transactions_purchase_line_id'],
            ];
        }

        // Create return transaction
        $returnTransaction = $this->PurchaseReturnService->create($purchaseSell, $data, $returnLinesArray);

        // Send notifications
        Notification::send(auth()->user(), new PurchaseNotification(
            $returnTransaction,
            $returnTransaction->TransactionPurchaseLines,
            'create',
            'تم اضافة فاتورة مرتجع مشتريات علي شراء برقم ' . $purchaseSell->ref_no . ' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($purchaseSell->created_at)->format('Y-m-d')
        ));

        // Log activity
        $this->ActivityLogsService->insert([
            'subject'       => $returnTransaction,
            'title'         => 'تم إضافة مرتجع مشتريات',
            'description'   => 'تم إضافة فاتورة مرتجع مشتريات بقيمة ' . $returnTransaction->total . ' لصالح ' .
                $purchaseSell->contact->name . ' في الفرع ' . $purchaseSell->Branch->name . ' رقم الفاتورة: ' .
                $returnTransaction->ref_no . ' بتاريخ ' . \Carbon\Carbon::parse($returnTransaction->transaction_date)->format('Y-m-d ') .
                '. تم تنفيذ المعاملة بواسطة المستخدم ' . auth()->user()->name . '.',
            'proccess_type' => 'purchase',
            'user_id'       => auth()->id(),
        ]);

        DB::commit();

        return redirect('dashboard/purchases/purchase-return')->with('success', 'تم اضافة فاتورة مرتجع مشتريات بنجاح');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'An error occurred: ' . $e->getMessage());
    }
}


    public function show($purchase_id){
        $transaction = Transaction::find($purchase_id);
        return [
            'title' => trans('admin.Show') . ' ' . trans('admin.purchase-return') . ' فاتورة رقم '  . $transaction->ref_no,
            'body'  => view('Dashboard.purchase-return.show')->with([
                'transaction' => $transaction,
            ])->render(),
        ];
    }
    public function delete($purchase_return_id){
        DB::beginTransaction();
        $purchase_return = Transaction::find($purchase_return_id);
        $this->PurchaseReturnService->delete($purchase_return);
        Notification::send(auth()->user(), new PurchaseNotification($purchase_return, $purchase_return->TransactionPurchaseLines, 'delete', 'تم حذف فاتورة مرتجع مشتريات علي فاتورة مشتريات برقم ' . $purchase_return->ref_no . ' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($purchase_return->created_at)->format('Y-m-d')));
        $this->ActivityLogsService->insert([
            'subject' => $purchase_return,
            'title' => 'تم حذف مرتجع مشتريات',
            'description' => 'تم حذف مرتجع مشتريات بقيمة ' . $purchase_return->final_price . ' لصالح ' . $purchase_return->contact->name . '.',
            'proccess_type' => 'purchase',
            'user_id' => auth()->id(),
        ]);
        DB::commit();
        return redirect('dashboard/purchases/purchase-return')->with('success', 'success');
    }
    public function printInvoicePage($purchase_return_id){
        $transaction = Transaction::with(['Contact', 'CreatedBy', 'TransactionSellLines.product', 'TransactionSellLines.Unit'])
        ->findOrFail($purchase_return_id);
    
        $settings = Setting::first();
        return view('Dashboard.purchase-return.printInvoice', compact('transaction', 'settings'));
    }
}
