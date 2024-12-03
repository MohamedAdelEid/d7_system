<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Brand;
use App\Models\Branch;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\StockService;
use App\Services\PurchaseService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ProductBranchDetails;
use App\Services\TransactionService;
use App\Services\ActivityLogsService;
use App\Models\TransactionPurchaseLine;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Notifications\PurchaseNotification;
use Illuminate\Support\Facades\Notification;

class PurchaseController extends Controller
{
    protected $transactionService;
    protected $purchaseService;
    protected $ActivityLogsService;
    public function __construct(TransactionService $transactionService, PurchaseService $purchaseService , ActivityLogsService $activityLogsService, StockService $StockService)
    {
        $this->transactionService = $transactionService;
        $this->purchaseService = $purchaseService;
        $this->StockService = $StockService;
        $this->ActivityLogsService = $activityLogsService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Transaction::with(['TransactionPurchaseLines'=>function($query){
                $query->where('return_quantity','>', 0);
            }])
            ->where('type', 'purchase')
                ->orderBy('id', 'desc');
            if($request->branch_id){
                $data->where('branch_id',$request->branch_id);
            }
            if($request->supplier_id){
                $data->where('contact_id',$request->supplier_id);
            }
            if($request->payment_status){
                $data->where('payment_status',$request->payment_status);
            }
            
            if($request->date_from && $request->date_to){
                $data->whereBetween('created_at', [$request->date_from, $request->date_to]);
            }
            if($request->created_by){
                $data->where('created_by', $request->created_by);
            }
            return DataTables::of($data)
                    ->addColumn('action', function($row){
                    $btn =  '<div class="btn-group"><button type="button" class="btn btn-success">'. trans('admin.Actions') .'</button><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"></button><div class="dropdown-menu" role="menu">';
                    $btn .= '<a class="dropdown-item fire-popup" data-url="' . route('dashboard.purchases.show', $row->id) . '"
                    href="#" data-toggle="modal" data-target="#modal-default-big">' . trans("admin.Show") . '</a>';
                    $btn .= '<a class="dropdown-item print-invoice" href="' . route('dashboard.purchases.printInvoicePage', $row->id) . '">' . __("admin.printInvoice") . '</a>';
                    $btn .= '<a class="dropdown-item" href="' . route('dashboard.purchases.edit', $row->id) . '" >' . trans('admin.Edit') . '</a>';
                    if (auth('user')->user()->has_permission('pay-purchases') && $row->payment_status != "final")
                        $btn .= '<a class="dropdown-item fire-popup"  data-toggle="modal" data-target="#modal-default" href="#" data-url="' . route('dashboard.purchases.payTransactionPurchasView', $row->id) . '">' . trans('admin.Pay') . '</a>';
                    if($row->ReturnTransactions()->count() == 0)
                    $btn .= '<a class="dropdown-item delete-popup"  href="#" data-toggle="modal" data-target="#modal-default" data-url="' . route('dashboard.purchases.destroy', $row->id) . '" >' . trans('admin.Delete') . '</a>';
                    if (auth('user')->user()->has_permission('create-purchase-return'))
                    $btn .= '<a class="dropdown-item fire-popup" href="" data-toggle="modal" data-target="#modal-default-big" data-url="'.route('dashboard.purchases.purchase-return.create', $row->id).'">' . trans('admin.add purchase-return') . '</a>';
                    if (auth('user')->user()->has_permission('change-delivery-status-purchases'))
                    $btn .= '<a class="dropdown-item fire-popup" href="#" data-toggle="modal" data-target="#modal-default" data-url="'
                        . route("dashboard.purchases.change-delivery-status", $row->id) . '">' . trans('admin.Change-Delivery-Status') . '</a>';


                    $btn .= '</div></div>';
                    return $btn;
                })
                ->addColumn('supplier_name', function ($row) {
                    return $row->contact->name ?? 'N/A';
                })
                
                ->addColumn('supplier_phone', function ($row) {
                    return $row->contact->phone ?? 'N/A';
                })
                ->addColumn('created_by', function($row){
                    return $row->CreatedBy->name ?? 'N/A';
                })
                ->addColumn('payment_status', function ($row) {
                    return $row->payment_status;
                })
                ->addColumn('delivery_status', function ($row) {
                    return $row->delivery_status;
                })
                ->addColumn('ref_no', function ($row) {
                    $hasReturnQuantity = $row->TransactionPurchaseLines->contains('return_quantity', '>', 0);
                    
                    if ($hasReturnQuantity) {
                        return $row->ref_no . ' <i class="fas fa-exchange-alt text-danger" title="Has Return"></i>';
                    }
                    
                    return $row->ref_no;
                })
                ->addColumn('paid_from_transaction', function ($row) {
                    return $row->PaymentsTransaction->sum('amount');
                })
                ->addColumn('paid_from_transaction', function ($row) {
                    $paidFromTRansaction    = $row->PaymentsTransaction->sum('amount');
                    $paymentsForReturnTransactions = $row->ReturnTransactions->flatMap(function ($returnTransaction) {
                        return $returnTransaction->PaymentsTransaction;
                    });
                    $SumPaymentsForReturnTransactions = $paymentsForReturnTransactions->sum('amount');
                   return  $paidFromTRansaction -   $SumPaymentsForReturnTransactions;
                   
                })
                ->addColumn('remaining_amount', function ($row) {
                    $paid_from_transaction = $row->PaymentsTransaction->sum('amount');
                    $paymentsForReturnTransactions = $row->ReturnTransactions->flatMap(function ($returnTransaction) {
                        return $returnTransaction->PaymentsTransaction;
                    });
                    $SumPaymentsForReturnTransactions = $paymentsForReturnTransactions->sum('amount');

                    return $row->final_price - ($paid_from_transaction - $SumPaymentsForReturnTransactions);
                })
                ->addColumn('route', function($row){
                    return route('dashboard.purchases.show', $row->id);
                })
                ->addColumn('branch_id', function($row){
                    return $row->Branch->name ?? 'N/A';
                })
                // ->addColumn('action', function ($row) {
                //     $btn .= '<a href="' . route('dashboard.purchases.show', $row->id) . '">View</a>';
                //     $btn .= '<a href="' . route('dashboard.purchases.printInvoicePage', $row->id) . '">' . __("admin.printInvoice") . '</a>';
                //     $btn .= '<a href="' . route('dashboard.purchases.edit', $row->id) . '" class="edit btn btn-info btn-sm mr-1">Edit</a>';
                //     $btn .= '<a href="' . route('dashboard.purchases.edit', $row->id) . '" class="edit btn btn-info btn-sm mr-1">Edit</a>';
                //     if($row->ReturnTransactions()->count() == 0)
                //         $btn .= '<a href="' . route('dashboard.purchases.destroy', $row->id) . '" class="delete btn btn-danger btn-sm mr-1">Delete</a>';
                                    
                //     if (auth('user')->user()->has_permission('create-purchase-return'))
                //         $btn .= '<a href="" class="edit btn btn-primary btn-sm mr-1 fire-popup mt-3" data-toggle="modal" data-target="#modal-default-big" data-url="'.route('dashboard.purchases.purchase-return.create', $row->id).'">' . trans('admin.add purchase-return') . '</a>';
                //     return $btn;
                // })
                ->rawColumns(['ref_no','action'])
                ->make(true);
        }
        $branches = Branch::active()->get();
        $suppliers = Contact::where('type', 'supplier')->active()->get();
        $users = User::all();
        return view('Dashboard.purchases.index', compact('branches', 'suppliers', 'users'));
    }

    public function show($id)
    {
        
        $purchase = Transaction::with( 'TransactionPurchaseLines.Product',
        'TransactionPurchaseLines.Unit',
        'purchaseUpdateHistories.updatedByUser',
        'ReturnTransactions')->find($id);
        return [
            'title' => trans('admin.Show') . ' فاتورة شراء رقم ' . $purchase->ref_no,
            'body'  => view('Dashboard.purchases.show')->with([
                'purchase' => $purchase,
            ])->render(),
        ];
        // return view('Dashboard.purchases.show', compact('purchase'));
    }

    public function create()
    {
        $branches = Branch::active()->FoMe()->get();
        $contacts = Contact::where('type', 'supplier')->active()->get();
        $brands = Brand::all();
        $products = [];
        $accounts = [];
        $cash_contact = Contact::where('is_default', 1)->first();

        $salesSegmentId = null;
        if ($cash_contact) {
            $salesSegmentId = $cash_contact->salesSegment?->id;
        }

        return view('Dashboard.purchases.create')->with([
            'brands'         => $brands,
            'contacts'       => $contacts,
            'branches'       => $branches,
            'products'       => $products,
            'accounts'       => $accounts,
            'cash_contact'   => $cash_contact,
            'salesSegmentId' => $salesSegmentId
        ]);

 
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id'      => ['required', 'exists:branchs,id'],
            'contact_id'     => ['required', 'exists:contacts,id'],
            'products'       => ['required', 'array', 'min:1'],
            'final_total'    => ['required', 'numeric'],
            'amount'         => ['required_if:sell_type,multi-pay', 'numeric', 'lte:final_total'],
        ], [
            'products.required' => 'اختر المنتجات',
            'branch_id.required' => 'اختر الفرع',
            'contact_id.required' => 'اختر العميل',
            'amount.required_if' => 'أدخل المبلغ',
            'amount.lte' => 'يجب أن يكون المبلغ مساوي أو أقل من الإجمالي النهائي'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
     
        DB::beginTransaction();  // Start the transaction
        try {
            // Find the branch by ID
            $branch = Branch::find($request->branch_id);
            if (!$branch) {
                return redirect()->route('dashboard.purchases.create')->with('error', trans('admin.branch not found'));
            }
        
            // Determine the account ID based on the sale type
            $account_id = null;
    
            if ($request->purchase_type == 'cash' || $request->purchase_type == "multi_pay") {
                $account_id = $branch->cash_account_id;
            } else if ($request->purchase_type == 'credit') {
                $account_id = $branch->credit_account_id;
            }
         
            // Check if account ID is linked
            if ($account_id == null) {
                return redirect()->route('dashboard.purchases.create')->with('error', trans('admin.you should link this branch to account'));
            }
           
            // Prepare the data for creating a purchase (transaction)
            $data = [
                'branch_id'        => $request->branch_id,
                'contact_id'       => $request->contact_id,
                'payment_type'     => $request->sell_type,
                'status'           => "final", // Assuming it's a final status at creation
                'account_id'       => $account_id,
                'discount_value'   => $request->discount_value,
                'discount_type'    => $request->discount_type,
                'delivery_status'  => $request->delivery_status, 
                'payment_type'     => $request->purchase_type == "multi_pay" || $request->purchase_type == "credit" ? 'credit' : 'cash',
            ];
       
            if ($request->has('amount')) {
                $data['amount'] = $request->amount;
            }
           
            // Process each product and prepare data for purchase lines
            $purchase_lines_array = [];
        
            foreach ($request->products as $productData) {
                $purchase_lines_array[] = [
                    'product_id' => $productData['id'],
                    'quantity'   => $productData['quantity'],
                    'unit_price' => $productData['unit_price'],
                    'unit_id'    => $productData['unit_id'],
                    'total'      => $productData['quantity'] * $productData['unit_price'],
                ];
            }
           
            // Create the purchase transaction
            $purchase = $this->purchaseService->CreatePurchase($data, $purchase_lines_array, $request);
           
            Notification::send(auth()->user(), new PurchaseNotification($purchase, $purchase->TransactionPurchaseLines, 'create', 'تم اضافة فاتورة شراء جديدة برقم ' . $purchase->ref_no . ' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($purchase->created_at)->format('Y-m-d')));
           
            // Log activity
            $this->ActivityLogsService->insert([
                'subject'     => $purchase,
                'title'       => 'تم إضافة شراء جديد',
                'description' => 'تم إضافة شراء جديد بقيمة ' . $purchase->final_price . ' لصالح ' . $purchase->contact->name . ' في الفرع ' . $purchase->branch->name . 
                ' بتاريخ ' . \Carbon\Carbon::parse($purchase->transaction_date)->format('Y-m-d ') . 
                ' بواسطة المستخدم ' . auth()->user()->name . ' من خلال ' . ($purchase->payment_type == 'cash' ? 'الدفع نقداً' : 'الشراء بالائتمان') . '، رقم الفاتورة: ' . $purchase->ref_no . 
                '. تم تطبيق خصم بقيمة ' . $purchase->discount_value . ' من نوع ' . ($purchase->discount_type == 'fixed_price' ? 'مبلغ ثابت' : 'نسبة مئوية') . '،',
                'proccess_type' => 'purchase',
                'user_id'     => auth()->id(),
            ]);
          
            // Commit the transaction
            DB::commit();

            return [
                'transaction' => $purchase,
            ];
      
            // return redirect()->route('dashboard.purchases.create')->with('success', trans('admin.success'));
    
        } catch (\Exception $e) {
            // Roll back the transaction on error
            DB::rollBack();
            return redirect()->route('dashboard.purchases.create')->with('error', trans('admin.error occurred') . ': ' . $e->getMessage());
        }
    }
    public function changeDeliveryStatus($purchase_id)
    {
        $transaction = Transaction::find($purchase_id);
        return [
            'title' => trans('admin.Change-Delivery-Status'),
            'body'  => view('Dashboard.purchases.change-delivery-status')->with([
                'transaction'   => $transaction
            ])->render(),
        ];
    }
    public function changeDeliveryStatusPost($purchase_id, Request $request)
    {
        try {
            $transaction = Transaction::findOrFail($purchase_id);
 
            $transaction->update([
                'delivery_status' => $request->delivery_status,
                'delivery_status_note' => $request->delivery_status_note ?? null,
            ]);
    
            if ($request->delivery_status == 'delivered') {
                $TransactionPurchaseLines = $transaction->TransactionPurchaseLines;
                $this->StockService->bulckAddToStockByPurchaseLines($transaction, $TransactionPurchaseLines);
            }
            
        
            $deliveryStatusMap = [
                'shipped' => 'تم الشحن',
                'ordered' => 'تم الطلب',
                'delivered' => 'تم التوصيل',
            ];
            Notification::send(auth()->user(), new PurchaseNotification($transaction, $transaction->TransactionPurchaseLines, 'update', 'تم تعديل حالة التوصيل لفاتورة شراء برقم ' . $transaction->ref_no . ' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d')));
            $this->ActivityLogsService->insert([
                'subject' => $transaction,
                'title' => 'تم تعديل حالة التوصيل لفاتورة شراء',
                'description' => 'تم تعديل حالة التوصيل من "' . $deliveryStatusMap[$transaction->delivery_status]  . '" إلى "' . $deliveryStatusMap[$request->delivery_status] 
                . '" بالملاحظة: "' . $request->delivery_status_note . '"، رقم الفاتورة: ' . $transaction->ref_no . ', بتاريخ ' . $transaction->created_at->format('Y-m-d'),
                'proccess_type' => 'purchase',
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()->with('success', trans('admin.success'));
    
        } catch (\Exception $e) {
            return redirect()->back()->with('error', trans('admin.error occurred') . ': ' . $e->getMessage());
        }

    }
    

    public function edit($id)
    {
        $purchase = Transaction::with('TransactionPurchaseLines')->find($id);
        $products = Product::all();
        $branches = Branch::active()->get();
        $brands = Brand::all();
        $contacts = Contact::where('type', 'supplier')->active()->get();
        $purchase_lines = $purchase->TransactionPurchaseLines;
        $cash_contact = Contact::where('is_default', 1)->first();
        $accounts = [];
        $purchaseService  = $this->purchaseService;
        return view('Dashboard.purchases.edit', compact('purchase','purchaseService','brands', 'cash_contact','products', 'branches', 'contacts', 'purchase_lines', 'accounts'));
    }
    public function update(Request $request, $id)
    {
        // return response($request);

        // Validate the request data
        $validatedData = $request->validate([
            'branch_id' => 'required|exists:branchs,id',
            'supplier_id' => 'required|exists:contacts,id',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:0',
            'products.*.unit_price' => 'required|numeric|min:0',
            'products.*.unit_id' => 'required|exists:units,id',
        ]);
     
        // Find the transaction
        $transaction = Transaction::findOrFail($id);

        if ($transaction->ReturnTransactions->count() > 0) {
            return redirect()->route('dashboard.purchases.edit', $id)->with('error', ' لا يمكن تعديل الفاتوره لانه حصل عليها مرتجع');
        }

        if($transaction->payment_status == "final" || $transaction->payment_status == "partial") {
            return redirect()->route('dashboard.purchases.edit',$id)->with('error','لا يمكن تعديل الفاتوره');
        }
        // Prepare the purchase data
        $data = [
            'branch_id' => $validatedData['branch_id'],
            'contact_id' => $validatedData['supplier_id'],
            'discount_value'   => $request->discount_value,
            'discount_type'    => $request->discount_type,
            'status' => 'final',
        ];

        // Prepare the purchase lines array
        $purchase_lines_array = [];
        foreach ($validatedData['products'] as $productData) {
            $purchase_lines_array[] = [
                'product_id' => $productData['id'],
                'quantity' => $productData['quantity'],
                'unit_price' => $productData['unit_price'],
                'unit_id' => $productData['unit_id'],
            ];
        }


        // Start transaction
        DB::beginTransaction();
        try {
            // Update purchase using the purchase service
            $this->purchaseService->UpdatePurchase($transaction, $data, $purchase_lines_array);
            Notification::send(auth()->user(), new PurchaseNotification($transaction, $transaction->TransactionPurchaseLines, 'update', 'تم تعديل عملية شراء برقم ' . $transaction->ref_no . ' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d')));
            // Log the activity
            $this->ActivityLogsService->insert([
                'subject' => $transaction,
                'title' => 'تم تعديل عملية شراء',
                'description' => 'تم تعديل عملية شراء بقيمة ' . $transaction->final_price .
                                 ' لصالح ' . $transaction->contact->name . 
                                 ' في الفرع ' . $transaction->branch->name . 
                                 ' رقم الفاتورة: ' . $transaction->ref_no . ' ' .
                                 ' بتاريخ ' . \Carbon\Carbon::parse($transaction->transaction_date)->format('Y-m-d ') . 
                                 ' باستخدام ' . ($transaction->payment_type == 'cash' ? 'الدفع نقداً' : 'الشراء بالائتمان') . 
                                 '. خصم : ' . ($transaction->discount_value ? 
                                 $transaction->discount_value . ' (' . 
                                 ($transaction->discount_type == 'fixed_price' ? 'مبلغ ثابت' : 'نسبة مئوية') . ')' : 'لا يوجد خصم') . 
                                 '. تم تنفيذ المعاملة بواسطة المستخدم ' . auth()->user()->name . '.',
                'proccess_type' => 'purchase',
                'user_id' => auth()->id(),
            ]);
    
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Handle the error, log it or display a message
            return redirect()->back()->withErrors(['error' => trans('admin.update_failed')]);
        }
    
        return redirect()->route('dashboard.purchases.index')->with('success', trans('admin.updated'));
    }
    

    public function destroy($id)
    {
        $transaction = Transaction::with('TransactionPurchaseLines')->find($id);
        DB::beginTransaction();
        $this->purchaseService->Delete($transaction);
        DB::commit();
        Notification::send(auth()->user(), new PurchaseNotification($transaction, $transaction->TransactionPurchaseLines, 'delete', 'تم حذف عملية شراء برقم ' . $transaction->ref_no . ' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d')));
        $this->ActivityLogsService->insert([
            'subject' => $transaction,
            'title' => 'تم حذف عملية شراء',
            'description' => 'تم حذف عملية شراء بقيمة ' . $transaction->final_price . ' لصالح ' . $transaction->contact->name . 
                             ' في الفرع ' . $transaction->branch->name . 
                             ' رقم الفاتورة: ' . $transaction->ref_no . ' ' .
                             ' بتاريخ ' . \Carbon\Carbon::parse($transaction->transaction_date)->format('Y-m-d ') . 
                             '. تم تنفيذ المعاملة بواسطة المستخدم ' . auth()->user()->name . '.',
            'proccess_type' => 'purchase',
            'user_id' => auth()->id(),
        ]);
        return redirect()->route('dashboard.purchases.index')->with('success', trans('admin.deleted'));
    }

    public function returnPurchase()
    {
        return view('Dashboard.purchases.returnPurchase');
    }

    public function ProductRowAdd(Request $request)
    {
        $product = Product::find($request->product_id);
        $product_row = $this->product_row($product, $request->branch_id);
        return view('Dashboard.purchases.parts.product_raw', compact('product_row'));
    }

    public function EditProductRowAdd(Request $request)
    {
        $product = Product::find($request->product_id);
        $product_row = $this->product_row($product, $request->branch_id);
        return view('Dashboard.purchases.parts.edit_product_raw', compact('product_row'));
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
            'quantity' => 0,
            'available_quantity' => $available_quantity,
            'purchase_price' => $product->purchase_price,
            'unit_price'    => $product->unit_pirce,
            'total' => 0,
            'min_sale' => $product->min_sale,
            'max_sale' => $product->max_sale,
        ];
    }

    public function printInvoicePage($id)
    {
  
        $transaction = Transaction::with([
            'TransactionPurchaseLines.product', 
            'TransactionPurchaseLines.unit', // Add this line to fetch unit details
            'Contact'
        ])
        ->where('type', 'purchase')
        ->where('id', $id)
        ->first();

        

        return view('Dashboard.purchases.printInvoicePage',compact('transaction'));
    }

    public function multiPay()
    {

        return [
            'title' => trans('admin.Pay'),
            'body'  => view('Dashboard.purchases.multiple-payment')->render(),
        ];
    }
}
