<?php

namespace App\Http\Controllers\Dashboard;

use Closure;
use App\Models\Unit;
use App\Models\User;
use App\Models\Brand;
use App\Traits\Stock;
use App\Models\Branch;
use App\Models\Account;
use App\Models\Contact;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\SellService;
use Illuminate\Support\Facades\DB;
use App\Models\TransactionSellLine;
use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;
use APP\Services\ActivityLogsService;
use Illuminate\Support\Facades\Redis;
use App\Notifications\SalesNotification;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Services\PaymentTransactionService;
use Illuminate\Support\Facades\Notification;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

class SellController extends Controller
{
    use Stock;

    public $SellService;
    protected $PaymentTransactionService;
    public $TransactionService;
    protected $ActivityLogsService;
    public function __construct(
        PaymentTransactionService $PaymentTransactionService,
        SellService $SellService,
        TransactionService $TransactionService,
        ActivityLogsService $ActivityLogsService
    ) {
        $this->SellService = $SellService;
        $this->PaymentTransactionService = $PaymentTransactionService;
        $this->TransactionService = $TransactionService;

        $this->ActivityLogsService = $ActivityLogsService;

        $this->middleware('permissionMiddleware:create-sells')->only(['create', 'store']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
           
            $data = Transaction::with(['PaymentsTransaction', 'ReturnTransactions.PaymentsTransaction','Contact','Contact.city','Contact.governorate',
                'TransactionSellLines' => function ($query) {
                    $query->where('return_quantity', '>', 0);
                }])
                ->where('type', 'sell')
                ->where('status', '!=', 'draft')
                ->orderBy('id', 'desc');
            if ($request->branch_id) {
                $data->where('branch_id', $request->branch_id);
            }
            if ($request->contact_id) {
                $data->where('contact_id', $request->contact_id);
            }
            if ($request->date_from && $request->date_to) {
                $data->whereBetween('created_at', [$request->date_from, $request->date_to]);
            }
            if ($request->created_by) {
                $data->where('created_by', $request->created_by);
            }
            if ($request->payment_status) {
                $data->where('payment_status', $request->payment_status);
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn =  '<div class="btn-group"><button type="button" class="btn btn-success">' . trans('admin.Actions') . '</button><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"></button><div class="dropdown-menu" role="menu">';
                    if (auth('user')->user()->has_permission('read-sells'))
                        $btn .= '<a class="dropdown-item fire-popup" data-url="' . route('dashboard.sells.show', $row->id) . '"
                                href="#" data-toggle="modal" data-target="#modal-default-big">' . trans("admin.Show") . '</a>';
                    if (auth('user')->user()->has_permission('update-sells') &&  $row->payment_status == "due")
                        $btn .= '<a class="dropdown-item"  href="' . route('dashboard.sells.edit', $row->id) . '">' . trans('admin.Edit') . '</a>';
                    if (auth('user')->user()->has_permission('pay-sells') && $row->payment_status != "final")
                        $btn .= '<a class="dropdown-item fire-popup"  data-toggle="modal" data-target="#modal-default" href="#" data-url="' . route('dashboard.sells.payTransaction', $row->id) . '">' . trans('admin.Pay') . '</a>';

                    if (auth('user')->user()->has_permission('printInvoicePage-sells')) {
                        $settings = Setting::first();
                        if ($settings->classic_printing) {
                            $btn .= '<a class="dropdown-item print-invoice"  href="' . route('dashboard.sells.printInvoicePage', $row->id) . '">' . trans('admin.Classic Printing') . '</a>';
                        } else {
                            $btn .= '<a class="dropdown-item print-invoice"  href="' . route('dashboard.sells.printThermalInvoice', $row->id) . '">' . trans('admin.Thermal Printing') . '</a>';
                        }
                    }
                    // if (auth('user')->user()->has_permission('printInvoice-sells'))
                    //     $btn .= '<a class="dropdown-item print-invoice" data-id="' .  $row->id  . '" style="cursor: pointer" >' . trans('admin.printInvoice') . '</a>';

                    if (auth('user')->user()->has_permission('create-sell-return') && $row->payment_status != "due")
                        $btn .= '<a href="" class="dropdown-item fire-popup" data-toggle="modal" data-target="#modal-default-big" data-url="' . route('dashboard.sells.sell-return.create', $row->id) . '">' . trans('admin.add sell-return') . '</a>';

                    if (auth('user')->user()->has_permission('delete-sells') && $row->ReturnTransactions()->count() == 0)
                        $btn .= '<a class="dropdown-item delete-popup" href="#" data-toggle="modal" data-target="#modal-default" data-url="' . route("dashboard.sells.delete", $row->id) . '">' . trans('admin.Delete') . '</a>';
                    if (auth('user')->user()->has_permission('change-delivery-status'))
                        $btn .= '<a class="dropdown-item fire-popup" href="#" data-toggle="modal" data-target="#modal-default" data-url="'
                            . route("dashboard.sells.change-delivery-status", $row->id) . '">' . trans('admin.Change-Delivery-Status') . '</a>';


                    $btn .= '</div></div>';
                    return $btn;
                })
                ->addColumn('contact_name', function ($row) {
                    return $row->Contact?->name;
                })
                ->addColumn('phone', function ($row) {
                    return $row->Contact?->phone;
                })
                ->addColumn('government', function ($row) {
                    
                    return $row->Contact?->governorate->governorate_name_ar ?? "";
                })
                ->addColumn('city_contact', function ($row) {
                    return $row->Contact->city->city_name_ar ?? "";
                })
                ->addColumn('total', function ($row) {
                    return number_format($row->final_price, 1);
                })
                ->addColumn('delivery_status', function ($row) {
                    return $row->delivery_status;
                })
                ->addColumn('payment_status', function ($row) {
                    return $row->payment_status;
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
                ->addColumn('created_by', function ($row) {
                    return $row->CreatedBy?->name;
                })
                ->addColumn('branch_id', function ($row) {
                    return $row->Branch?->name;
                })->addColumn('route', function ($row) {
                    return route('dashboard.sells.show', $row->id);
                })
                ->addColumn('ref_no', function ($row) {
                    $hasReturnQuantity = $row->TransactionSellLines->contains('return_quantity', '>', 0);
                    
                    if ($hasReturnQuantity) {
                        return $row->ref_no . ' <i class="fas fa-exchange-alt text-danger" title="Has Return"></i>';
                    }
                    
                    return $row->ref_no;
                })
                ->rawColumns(['ref_no', 'action']) 
                ->make(true);
        }
        $branches = Branch::active()->get();
        $customers = Contact::where('type', 'customer')->get();
        $users = User::all();
        return view('Dashboard.sells.index', compact('branches', 'customers', 'users'));
    }
    public function allDraft(Request $request)
    {
        if ($request->ajax()) {
            $data = Transaction::with('PaymentsTransaction', 'ReturnTransactions.PaymentsTransaction')->where('type', 'sell')->where('status', 'draft')
                ->orderBy('id', 'desc');
            if ($request->branch_id) {
                $data->where('branch_id', $request->branch_id);
            }
            if ($request->contact_id) {
                $data->where('contact_id', $request->contact_id);
            }
            if ($request->date_from && $request->date_to) {
                $data->whereBetween('created_at', [$request->date_from, $request->date_to]);
            }
            if ($request->created_by) {
                $data->where('created_by', $request->created_by);
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('contact', function ($row) {
                    return $row->Contact?->name;
                })
                ->addColumn('phone', function ($row) {
                    return $row->Contact?->phone;
                })
                ->addColumn('government', function ($row) {
                    return $row->Contact?->government;
                })
                ->addColumn('city', function ($row) {
                    return $row->Contact?->city;
                })
                ->addColumn('total', function ($row) {
                    return number_format($row->final_price, 1);
                })

                ->addColumn('created_by', function ($row) {
                    return $row->CreatedBy?->name;
                })
                ->addColumn('transaction_from', function ($row) {
                    return $row->transaction_from ?? 'dashboard';
                })
                ->addColumn('branch_id', function ($row) {
                    return $row->Branch->name;
                })->addColumn('route', function ($row) {
                    return route('dashboard.sells.draft.edit', $row->id);
                })
                ->addColumn('ref_no', function ($row) {
                    return $row->ref_no;
                })
                ->make(true);
        }
        $branches = Branch::active()->get();
        $customers = Contact::where('type', 'customer')->get();
        $users = User::all();
        return view('Dashboard.sells.drafts.index', compact('branches', 'customers', 'users'));
    }
    public function changeDeliveryStatus($sell_id)
    {
        $transaction = Transaction::find($sell_id);
        return [
            'title' => trans('admin.Change-Delivery-Status'),
            'body'  => view('Dashboard.sells.change-delivery-status')->with([
                'transaction'   => $transaction
            ])->render(),
        ];
    }
    public function changeDeliveryStatusPost($sell_id, Request $request)
    {
        $transaction = Transaction::find($sell_id);
        $transactionSellLine = TransactionSellLine::where('transaction_id', $sell_id)->get()->first();
        $transaction->update([
            'delivery_status'   => $request->delivery_status,
            'delivery_status_note'   => $request->delivery_status_note,
        ]);
        Notification::send(auth()->user(), new SalesNotification($transaction, $transactionSellLine, 'update', 'تم تعديل حالة توصيل فاتورة بيع برقم ' . $transaction->ref_no . ' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d')));

        $this->ActivityLogsService->insert([
            'subject' => $transaction,
            'title' => 'تم تعديل حالة فاتورة بيع',
            'description' => 'تم تعديل الحالة  لفاتورة بيع برقم ' . $transaction->ref_no . ' من ' . $transaction->delivery_status . ' إلى ' . $request->delivery_status . ' في ' . now()->format('F j, Y g:i A') . '.',
            'proccess_type' => 'sales',
            'user_id' => auth()->id(),
        ]);
        return redirect()->back()->with('success', trans('admin.success'));
    }
    public function create()
    {
        $settings = Setting::first();
        $branches = Branch::active()->FoMe()->get();
        $brands   = Brand::all();
        $contacts = Contact::active()->where('type', 'customer')->get();
        $products = [];
        $accounts = [];
        $cash_contact = Contact::where('is_default', 1)->first();

        $salesSegmentId = null;
        if ($cash_contact) {
            $salesSegmentId = $cash_contact->salesSegment->id ?? "";
        }

        return view('Dashboard.sells.create')->with([
            'brands'         => $brands,
            'contacts'       => $contacts,
            'branches'       => $branches,
            'products'       => $products,
            'accounts'       => $accounts,
            'cash_contact'   => $cash_contact,
            'salesSegmentId' => $salesSegmentId,
            'settings'       => $settings
        ]);
    }

    // Function to add a product row
    public function ProductRowAdd(Request $request)
    {

        try {
          
            $contact = Contact::find($request->contact_id);
            // Find the product based on the provided product ID
            $product = Product::with('MainUnit', 'salesSegments', 'units')->find($request->product_id);
          
            if (!$product) {
                return response()->json(['error' => 'Product not found'], 404);
            }
        
            $branchId = $request->branch_id;

            $available_quantity = $product->getStockByBranch($branchId);
           
            $segmentPrice = $contact && $contact->salesSegment
                ? $product->getSalePriceByUnitAndSegment($product->MainUnit->id, $contact->salesSegment->id)
                : $product->getSalePriceByUnit($product->MainUnit->id);
           
            $purchase_price = $product->getPurchasePriceByUnit($product->MainUnit->id);
            $last_sale_price = $product->TransactionSellLines()
                ->whereHas('Transaction', function ($query) use ($contact) {
                    // Optionally filter by the client (contact)
                    $query->where('contact_id', $contact->id);
              
                })
               ->where('unit_id', $product->MainUnit->id)
                ->latest('created_at') // Get the latest transaction
                ->first()->unit_price ?? false ;
             

            // Prepare the product row details
            $product_row = [
                'id' => $product->id,
                'name' => $product->name,
                'segmentPrice' => $segmentPrice,
                'last_sale_price' => $last_sale_price,
                'unit_price' => $segmentPrice,
                'available_quantity' => $available_quantity,
                'quantity' => $product->min_sale,
                'MainUnit' => $product->MainUnit,
                'unit' => $product->MainUnit->id,
                'units' => $product->units,
                'total' => $product->segmentPrice,
                'min_sale' => $product->min_sale,
                'max_sale' => $product->max_sale,
                'purchase_price' => $purchase_price,
            ];

            return response()->json($product_row);
        } catch (\Exception $e) {
            \Log::error('Error in ProductRowAdd: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function getLastProductPriceByUnit(Request $request)
    {

        return 'test';
    }
    // Function to search for products based on branch and query
    public function searchProducts(Request $request)
    {
        
        $branchId = $request->input('branch_id');
        $searchTerm = $request->input('query');
        $contact = Contact::find($request->contact_id);
        // Fetch products based on the branch and search term
        $products = Product::where('for_sale', true)
            ->whereHas('branches', function ($query) use ($branchId) {
                $query->withoutGlobalScope('getBranchesByUserAuth')
                    ->where('branchs.id', $branchId);
            })
            ->where(function ($query) use ($searchTerm) {
                $query->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('sku', 'LIKE', "%{$searchTerm}%");
            })
            ->get();

       
        // Get available quantities for each product
        $productsWithStock = $products->map(function ($product) use ($branchId, $contact) {

            $sellPriceByMainUnit = $product->getSellPrice();

            $purchasePriceByMainUnit = $product->getPurchasePriceByUnit($product->unit_id) ?? "";
           
            $segmentPrice = $contact && $contact->salesSegment
                ? $product->getSalePriceByUnitAndSegment($product->unit_id, $contact->salesSegment->id)
                : $product->getSellPrice();
           
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'unit_price' =>  $segmentPrice ?? $sellPriceByMainUnit,
                'purchase_price' => $purchasePriceByMainUnit,
                'available_quantity' => $product->getStockByBranch($branchId), // Get stock based on branch
            ];
        });

        return response()->json($productsWithStock);
    }


    public function store(Request $request)
    {

       
        $validator = Validator::make($request->all(), [
            'branch_id'      => ['required', 'exists:branchs,id'],
            'contact_id'     => ['required', 'exists:contacts,id'],
            'products'       => ['required', 'array', 'min:1'],
            'final_total'    => ['required', 'numeric'],
            'amount'         => ['required_if:sell_type,multi-pay', 'numeric', 'lte:final_total'],
            'products.*'       => [function (string $attribute, mixed $value, Closure $fail) use ($request) {

                $product    = Product::find($value['product_id']);
                $availableQuantity = $product->getStockByBranch($request->branch_id);
                $quantityByUnit = $this->getMainUnitQuantityFromSubUnit($product, $value['unit_id'], $value['quantity']);
                if ($quantityByUnit > $availableQuantity) {
                    $fail("لقد تجاوزت  الكمية المتاحة للمنتج $product->name");
                }
            }],
            'sell_type' => [function (string $attribute, mixed $value, Closure $fail) use ($request) {
                if ($value === 'credit') {
                    $contact = Contact::find($request->contact_id);
                    $balance = Contact::find($request->contact_id)->balance;
                    if ($request->final_total + $balance > $contact->credit_limit) {
                        $fail("لقد تجاوزت الحد الائتماني");
                    }
                }
            },]
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

        DB::beginTransaction();

        try {
            // Find the branch by ID
            $branch = Branch::find($request->branch_id);
            if (!$branch) {
                throw new \Exception(trans('admin.branch not found'));
            }

            // Determine the account ID based on the sale type
            $account_id = null;
            if ($request->sell_type == 'cash' || $request->sell_type == 'multi_pay' || $request->sell_type == 'draft') {
                $account_id = $branch->cash_account_id;
            } else if ($request->sell_type == 'credit') {
                $account_id = $branch->credit_account_id;
            }


            // Check if account ID is linked
            if ($account_id == null) {
                throw new \Exception(trans('admin.you should link this branch to account'));
            }

            // Prepare the data for creating a sale
            $data = [
                'branch_id' => $request->branch_id,
                'contact_id' => $request->contact_id,
                'payment_type' => $request->sell_type == "multi_pay" || $request->sell_type == "credit" ? 'credit' : 'cash',
                'status' => $request->sell_type == "draft" ? 'draft' : 'final',
                'account_id' => $account_id,
                'discount_value' => $request->discount_value,
                'discount_type' => $request->discount_type,
                'date' => $request->date,
            ];

            if ($request->has('amount')) {
                $data['amount'] = $request->amount;
            }


            // Create the sale transaction
            $transaction = $this->SellService->CreateSell($data, $request->products, $request);
            Notification::send(auth()->user(), new SalesNotification($transaction, $transaction->TransactionSellLines, 'create', 'تم اضافة فاتورة بيع جديدة برقم ' . $transaction->ref_no . ' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d')));
            // Log the activity only if transaction was created successfully
            if ($transaction) {
                $this->ActivityLogsService->insert([
                    'subject' => $transaction,
                    'title' => 'تم إضافة معاملة بيع جديدة',
                    'description' => 'تم إضافة معاملة بيع جديدة بقيمة ' . $transaction->final_price . ' لصالح ' . $transaction->Contact->name .
                        ' في الفرع ' . $transaction->Branch->name .
                        ' رقم الفاتورة: ' . $transaction->ref_no . ' ' .
                        ' بتاريخ ' . $transaction->transaction_date->format('Y-m-d ') .
                        ' باستخدام ' . ($transaction->payment_type == 'cash' ? 'الدفع نقداً' : 'الشراء بالائتمان') .
                        '. خصم : ' . ($transaction->discount_value ? $transaction->discount_value . ' (' . ($transaction->discount_type == 'fixed_price' ? 'مبلغ ثابت' : 'نسبة مئوية') . ')' : 'لا يوجد خصم') .
                        '. تم تنفيذ المعاملة بواسطة المستخدم ' . auth()->user()->name . '.',
                    'proccess_type' => 'sales',
                    'user_id' => auth()->id(),
                ]);
            }

            DB::commit();
            if ($request->sell_type == "draft") {
                return redirect()->route('dashboard.sells.drafts.index')->with(['success' => trans('admin.success')]);
            }
            return [
                'transaction' => $transaction,
            ];
            // return redirect()->route('dashboard.sells.create')->with(['success' => trans('admin.success'), 'transaction' => $transaction]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            // return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }


    public function AddBulckProductsPopUp()
    {
        $brands = Brand::get();
        return [
            'title' => trans('admin.Add Bulck products'),
            'body'  => view('Dashboard.sells.parts.AddBulckProductsPopUp')->with([
                'brands' => $brands,
            ])->render(),
        ];
    }
    public function AddBulckProductsAjax(Request $request)
    {
        $branchId = $request->input('branch_id') ?? Auth::user()->branch_id;
        $products = Product::HasStock(Request()->branch_id)
            ->where('brand_id', Request()->brand_id)
            ->whereHas('Branches', function ($query) use ($branchId) {
                $query->where('branchs.id', $branchId); // Specify the table name
            })
            ->get();

        $html = '';
        foreach ($products as $product) {
            $product_row = $this->SellService->product_row($product, Request()->branch_id);
            $html .= view('Dashboard.sells.parts.product_row')->with([
                'product_row'   => $product_row
            ]);
        }
        return $html;
    }

    public function AddBulckProductsInsertAjax(Request $request)
    {
        $html = '';
        foreach ($request->products as $product_row) {
            if ($product_row['quantity'] > 0) {
                $product = Product::find($product_row['id']);
                $data = [
                    'id'    => $product_row['id'],
                    'name' => $product_row['name'],
                    'units' => $product->units,
                    'quantity'  => $product_row['quantity'],
                    'available_quantity' =>  $product_row['available_quantity'],
                    'unit_price'    => $product_row['unit_price'],
                    'total'    => $product_row['total'],
                    'min_sale'  => 0,
                    'max_sale'  => $product['max_sale'],
                    'unit_id' => $product_row['unit_id']
                ];

                $html .= view('Dashboard.sells.parts.product_row')->with([
                    'product_row'   => $data
                ]);
            }
        }
    }

    public function getProductsByBrand(Request $request)
    {
        // Validate brand_id
        $request->validate([
            'brand_id' => 'required|exists:brands,id',
        ]);

        // Get branch_id from the request or use the user's branch_id
        $branchId = $request->input('branch_id', Auth::user()->branch_id);
        $brandId = $request->input('brand_id');
        $contactId = $request->input('contact_id');

        // Check if contactId is provided and valid
        $contact = $contactId ? Contact::find($contactId) : null;

        // Fetch products for the specified brand
        $products = Product::with('MainUnit', 'salesSegments', 'units')
            ->where('brand_id', $brandId)
            ->get()
            ->map(function ($product) use ($branchId, $contact) {
                // Get the unit price based on the contact's sales segment, if available
                $unit_price = $contact && $contact->salesSegment
                    ? $product->getSalePriceByUnitAndSegment($product->MainUnit->id, $contact->salesSegment->id)
                    : $product->getSalePriceByUnit($product->MainUnit->id);
                if (!$unit_price) {
                    $unit_price = 0;
                }

                // $last_sale_price = $product->TransactionSellLines()
                // ->whereHas('Transaction', function ($query) use ($contact) {
                //     // Optionally filter by the client (contact)
                //     $query->where('contact_id', $contact->id);
                // })
                // ->latest('created_at') // Get the latest transaction
                // ->first();

                // $last_sale_price = $last_sale_price ? $last_sale_price->unit_price : $unit_price;
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'unit' => $product->MainUnit->id,
                    'units' => $product->units,
                    // 'last_sale_price' => $last_sale_price,
                    'available_quantity' => $product->getStockByBranch($branchId),
                    'unit_price' => $product->getSellPrice(),
                    'total' => $unit_price, // Adjust if total is meant to represent more than the unit price
                    'min_sale' => $product->min_sale,
                    'max_sale' => $product->max_sale,
                    'purchase_price' => floatval($product->getPurchasePrice()),
                ];
            });

        return response()->json($products, 200); // Add status code for success
    }


    public function show($sell_id)
    {
        $transaction = Transaction::find($sell_id);
        return [
            'title' => trans('admin.Show') . ' فاتورة بيع رقم ' . $transaction->ref_no,
            'body'  => view('Dashboard.sells.show')->with([
                'transaction' => $transaction,
            ])->render(),
        ];
    }

    public function delete($sell_id)
    {
        $sell = Transaction::find($sell_id);
        DB::beginTransaction();
        $this->SellService->delete($sell);
        Notification::send(auth()->user(), new SalesNotification($sell, $sell->TransactionSellLines, 'delete', 'تم حذف فاتورة بيع برقم ' . $sell->ref_no . ' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($sell->created_at)->format('Y-m-d')));
        $this->ActivityLogsService->insert([
            'subject' => $sell,
            'title' => 'تم حذف معاملة بيع',
            'description' => 'تم حذف معاملة بيع بقيمة ' . $sell->final_price . ' لصالح ' . $sell->Contact->name .
                ' في الفرع ' . $sell->Branch->name .
                ' رقم الفاتورة: ' . $sell->ref_no .
                ' بتاريخ ' . \Carbon\Carbon::parse($sell->transaction_date)->format('Y-m-d') .
                '. تم حذف المعاملة من قبل المستخدم ' . auth()->user()->name . '.',
            'proccess_type' => 'sales',
            'user_id' => auth()->id(),
        ]);
        DB::commit();
        return redirect('dashboard/sells')->with('success', 'success');
    }

    public function edit($sell_id)
    {
        $branches = Branch::active()->get();
        $brands   = Brand::all();
        $contacts = Contact::active()->where('type', 'customer')->get();
        $sell = Transaction::find($sell_id);
        $products = [];
        $accounts = [];
        $cash_contact = Contact::where('is_default', 1)->first();
        $salesSegmentId = null;
        if ($cash_contact) {
            $salesSegmentId = $cash_contact->salesSegment?->id;
        }

        return view('Dashboard.sells.edit')->with([
            'brands'    => $brands,
            'contacts'  => $contacts,
            'branches'  => $branches,
            'products'  => $products,
            'sell'  => $sell,
            'SellService'  => $this->SellService,
            'accounts'  => $accounts,
            'salesSegmentId'  => $salesSegmentId,

        ]);
    }
    public function draftEdit($sell_id)
    {
        $branches = Branch::active()->get();
        $brands   = Brand::all();
        $contacts = Contact::active()->where('type', 'customer')->get();
        $sell = Transaction::find($sell_id);
        $products = [];
        $accounts = [];
        $cash_contact = Contact::where('is_default', 1)->first();
        $salesSegmentId = null;
        if ($cash_contact) {
            $salesSegmentId = $cash_contact->salesSegment?->id;
        }

        return view('Dashboard.sells.drafts.edit')->with([
            'brands'    => $brands,
            'contacts'  => $contacts,
            'branches'  => $branches,
            'products'  => $products,
            'sell'  => $sell,
            'SellService'  => $this->SellService,
            'accounts'  => $accounts,
            'salesSegmentId'  => $salesSegmentId,

        ]);
    }
    public function loadSellProducts($sell_id)
    {
        try {
            // جلب عملية البيع بناءً على الـ ID
            $sell = Transaction::with('TransactionSellLines.Product.MainUnit')->find($sell_id);

            // التحقق مما إذا كانت عملية البيع موجودة
            if (!$sell) {
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            $products = [];

            // استعراض خطوط البيع لجمع بيانات المنتجات
            foreach ($sell->TransactionSellLines as $line) {
                $products[] = [
                    'id' => $line->Product->id,
                    'name' => $line->Product->name,
                    'unit_price' => $line->unit_price,
                    'available_quantity' => $line->available_quantity,
                    'quantity' => $line->quantity,
                    'unit' => $line->Product->MainUnit->actual_name,
                    'total' => $line->quantity * $line->unit_price,
                ];
            }

            // إرجاع البيانات كاستجابة JSON
            return response()->json($products);
        } catch (\Exception $e) {
            // تسجيل الخطأ وإرجاع رسالة خطأ
            \Log::error('Error in loadSellProducts: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function update($sell_id, Request $request)
    {


        $sell = Transaction::with('ReturnTransactions')->find($sell_id);
     
        if ($sell->ReturnTransactions->count() > 0) {
            return redirect()->route('dashboard.sells.edit', $sell_id)->with('error', ' لا يمكن تعديل الفاتوره لانه حصل عليها مرتجع');
        }

        if ($sell->payment_status == "final" || $sell->payment_status == "partial") {
            return redirect()->route('dashboard.sells.edit', $sell_id)->with('error', 'لا يمكن تعديل الفاتوره');
        }

        DB::beginTransaction();
        $data = [
            'status'  => "final",
            'discount_value' => $request->discount_value,
            'discount_type' => $request->discount_type,
        ];
        $this->SellService->UpdateSell($sell, $data, $request->products);
        Notification::send(auth()->user(), new SalesNotification($sell, $sell->TransactionSellLines, 'update', 'تم تعديل حالة فاتورة بيع برقم ' . $sell->ref_no . ' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($sell->created_at)->format('Y-m-d')));
        $this->ActivityLogsService->insert([
            'subject' => $sell,
            'title' => 'تم تعديل معاملة بيع',
            'description' => 'تم تعديل معاملة بيع بقيمة ' . $sell->final_price . ' لصالح ' . $sell->Contact->name .
                ' في الفرع ' . $sell->Branch->name .
                ' رقم الفاتورة: ' . $sell->ref_no .
                ' بتاريخ ' . \Carbon\Carbon::parse($sell->transaction_date)->format('Y-m-d') .
                '. تم تحديث المعاملة من قبل المستخدم ' . auth()->user()->name .
                '. حالة الدفع: ' . ($sell->payment_type == 'cash' ? 'الدفع نقداً' : 'الشراء بالائتمان') .
                '. الخصم: ' . ($sell->discount_value ? $sell->discount_value . ' (' . ($sell->discount_type == 'fixed_price' ? 'مبلغ ثابت' : 'نسبة مئوية') . ')' : 'لا يوجد خصم') .
                '.',
            'proccess_type' => 'sales',
            'user_id' => auth()->id(),
        ]);

        DB::commit();
        return redirect('dashboard/sells')->with('success', 'success');
    }

    public function finishSell($sell_id, Request $request)
    {
        $sell = Transaction::find($sell_id);
        $branch = Branch::find($sell->branch_id);
        DB::beginTransaction();

        $account_id = null;
        if ($request->sell_type == 'cash' || $request->sell_type == 'multi_pay') {
            $account_id = $branch->cash_account_id;
        } else if ($request->sell_type == 'credit') {
            $account_id = $branch->credit_account_id;
        }
        $data = [
            'payment_type' => $request->sell_type == "multi_pay" ? 'credit' : $request->sell_type,
            'status' => 'final',
            'account_id' => $account_id,
            'discount_value' => $request->discount_value,
            'discount_type' => $request->discount_type,
        ];

        if ($request->has('amount')) {
            $data['amount'] = $request->amount;
        }
        $this->SellService->FinishSell($sell, $data, $request->products);
        Notification::send(auth()->user(), new SalesNotification($sell, $sell->TransactionSellLines, 'update', 'تم تعديل حالة فاتورة بيع برقم ' . $sell->ref_no . ' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($sell->created_at)->format('Y-m-d')));
        $this->ActivityLogsService->insert([
            'subject' => $sell,
            'title' => 'تم  معاملة بيع',
            'description' => 'تم  معاملة بيع بقيمة ' . $sell->final_price . ' لصالح ' . $sell->Contact->name .
                ' في الفرع ' . $sell->Branch->name .
                ' رقم الفاتورة: ' . $sell->ref_no .
                ' بتاريخ ' . \Carbon\Carbon::parse($sell->transaction_date)->format('Y-m-d') .
                '. تم تحديث المعاملة من قبل المستخدم ' . auth()->user()->name .
                '. حالة الدفع: ' . ($sell->payment_type == 'cash' ? 'الدفع نقداً' : 'الشراء بالائتمان') .
                '. الخصم: ' . ($sell->discount_value ? $sell->discount_value . ' (' . ($sell->discount_type == 'fixed_price' ? 'مبلغ ثابت' : 'نسبة مئوية') . ')' : 'لا يوجد خصم') .
                '.',
            'proccess_type' => 'sales',
            'user_id' => auth()->id(),
        ]);

        DB::commit();
        return redirect('dashboard/sells')->with('success', 'success');
    }

    public function printInvoicePage($id)
    {
        $settings = Setting::first();

        $transaction = Transaction::with(
            "TransactionSellLines.product",
            "TransactionSellLines.Unit",
            'PaymentsTransaction',
            "Contact"
        )
            ->where('type', 'sell')
            ->where('id', $id)
            ->first();

        $sumTransactionBefore = Transaction::withSum('PaymentsTransaction', 'amount')
            ->where(function ($q) {
                $q->where('type', 'sell')
                    ->orWhere('type', 'opening_balance');
            })
            ->where('contact_id', $transaction->contact_id)
            ->where('transaction_date', '<', $transaction->transaction_date)
            ->sum('final_price');


        $sumTotalPaymentBefore = Payment::where('contact_id', $transaction->contact_id)
            ->where('created_at', '<', $transaction->transaction_date)
            ->where(function ($query) {
                $query->whereNull('for')
                    ->orWhere('for', '<>', 'decrement_opening_balance');
            })
            ->sum('amount');

        $totalBeforeDue = $sumTransactionBefore - $sumTotalPaymentBefore;

        $toalAfterDue = $totalBeforeDue;

        if ($transaction->payment_status == "due" && $transaction->payment_type == 'credit') {

            $toalAfterDue += $transaction->final_price;
        } else if ($transaction->payment_status == "partial" && $transaction->payment_type == 'credit') {

            if ($totalBeforeDue < 0) {
                $toalAfterDue +=   $transaction->final_price;
            } else {
                $toalAfterDue +=  $transaction->final_price - $transaction->PaymentsTransaction->sum('amount');
            }
        } else {
            if ($totalBeforeDue < 0) {
                $toalAfterDue +=   $transaction->final_price;
            }
        }

        return view('Dashboard.sells.printInvoicePage', compact('transaction', 'totalBeforeDue', 'settings', 'toalAfterDue'));
    }
    public function printThermalInvoice($id)
    {
        $settings = Setting::first();
        $transaction = Transaction::with(
            "TransactionSellLines.product",
            "TransactionSellLines.Unit",
            'PaymentsTransaction',
            "Contact"
        )
            ->where('type', 'sell')
            ->where('id', $id)
            ->first();

        $totalBeforeDue = Transaction::withSum('PaymentsTransaction', 'amount')
            ->where('type', 'sell')
            ->where('contact_id', $transaction->contact_id)
            ->where('transaction_date', '<', $transaction->transaction_date)
            ->get()
            ->sum(function ($q) {
                return $q->final_price - $q->payments_transaction_sum_amount;
            });

        return view('Dashboard.sells.print-thermal-Invoice', compact('transaction', 'totalBeforeDue', 'settings'));
    }
    public function getSegmentPrices(Request $request)
    {
        $contact = Contact::find($request->contact_id);
        $prices = [];

        if ($contact && $contact->salesSegment) {
            $branchProducts = Product::hasStock($request->branch_id)->get();

            foreach ($branchProducts as $product) {
                $segmentPrice = $product->getPriceBySalesSegment($contact->salesSegment->id);
                $prices[$product->id] = $segmentPrice ?? $product->unit_price;
            }
        }

        return response()->json(['prices' => $prices]);
    }
    public function multiPay()
    {
        $accounts = Account::get();

        return [
            'title' => trans('admin.Pay'),
            'body'  => view('Dashboard.sells.multiple-payment')->with([
                'accounts' => $accounts,
            ])->render(),
        ];
    }

    public function ProductRowAddStock(Request $request)
    {
        try {
            $product = Product::with('MainUnit', 'units')->find($request->product_id);

            if (!$product) {
                return response()->json(['error' => 'Product not found'], 404);
            }

            $branchId = $request->branch_id;
            $available_quantity = $product->getStockByBranch($branchId);

            $product_row = [
                'id' => $product->id,
                'name' => $product->name,
                'available_quantity' => $available_quantity,
                'quantity' => 1,
                'MainUnit' => $product->MainUnit,
                'unit' => $product->MainUnit->id,
                'units' => $product->units,
            ];

            return response()->json($product_row);
        } catch (\Exception $e) {
            \Log::error('Error in ProductRowAddStock: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

}
