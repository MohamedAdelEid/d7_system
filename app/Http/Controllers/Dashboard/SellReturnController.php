<?php

namespace App\Http\Controllers\Dashboard;

use Closure;
use Carbon\Carbon;
use App\Models\Branch;
use App\Models\Account;
use App\Models\Contact;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\SellReturnService;
use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use APP\Services\ActivityLogsService;
use App\Notifications\SalesNotification;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Notification;

class SellReturnController extends Controller
{
    public $SellReturnService;
    public $TransactionService;
    public $ActivityLogsService;
    public function __construct(SellReturnService $SellReturnService,
                                TransactionService $TransactionService,
                                ActivityLogsService $ActivityLogsService){
        $this->SellReturnService = $SellReturnService;
        $this->TransactionService = $TransactionService;
        $this->ActivityLogsService = $ActivityLogsService;
    }
    
    public function index(Request $request){
        if ($request->ajax()) {
            $data = Transaction::where('type', 'sell_return')
                                ->orderBy('id', 'desc');

            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn =  '<div class="btn-group"><button type="button" class="btn btn-success">'. trans('admin.Actions') .'</button><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"></button><div class="dropdown-menu" role="menu">';
                        if (auth('user')->user()->has_permission('read-sell-return')) 
                            $btn .= '<a class="dropdown-item fire-popup" data-url="'.route('dashboard.sells.sell-return.show', $row->id).'" href="#" data-toggle="modal" data-target="#modal-default-big">' . trans("admin.Show") . '</a>';

                        if (auth('user')->user()->has_permission('delete-sell-return') && $row->TransactionFromReturnTransaction)
                            $btn .= '<a class="dropdown-item delete-popup" href="#" data-toggle="modal" data-target="#modal-default" data-url="'.route("dashboard.sells.sell-return.delete", $row->id).'">' . trans('admin.Delete') . '</a>';
                        
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
                    ->addColumn('total', function($row){
                        return number_format($row->total,1);
                    })->addColumn('route', function ($row) {
                        return route('dashboard.sells.sell-return.show', $row->id);
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('Dashboard.sell-return.index');
    }

    public function create($sell_id){
        $transaction = Transaction::find($sell_id);
        return [
            'title' => trans('admin.sell-return'),
            'body'  => view('Dashboard.sell-return.create')->with([
                'transaction'   => $transaction
            ])->render(),
        ];
    }

    public function store(Request $request){
        
        // $request->validate([
        //     'transaction_id'       => [function (string $attribute, mixed $value, Closure $fail) use ($request) {

        //         $main_sell = Transaction::find($request->transaction_id);
        
        //         if ($main_sell->payment_status == "due") {
        //             $fail("لا يمكن عمل مرتجع على هذه الفاتوره لانها غير مدفوعه");
        //         }
        //     }],
        // ]);

        DB::beginTransaction();
        $main_sell = Transaction::find($request->transaction_id);
        $data = [
            'branch_id'  => $main_sell->branch_id,
            'contact_id'  => $main_sell->contact_id,
            'status'  => "final",
            'payment_type'  =>  $main_sell->payment_type,
        ];
        $return_lines_array = [];
        foreach($request->products_return as $item){
            $return_lines_array[] = [
                'product_id'  => $item['product_id'],
                'quantity'  => $item['return_quantity'],
                'unit_price'  => $item['unit_price'],
                'unit_id'  => $item['unit_id'],
                'transactions_sell_line_id'  => $item['transactions_sell_line_id'],
            ];
        }
        $this->SellReturnService->create($main_sell, $data, $return_lines_array);
        Notification::send(auth()->user(), new SalesNotification($main_sell, $main_sell->TransactionSellLines, 'create', 'تم اضافة فاتورة بيع مرتجع علي فاتورة بيع برقم ' . $main_sell->ref_no . ' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($main_sell->created_at)->format('Y-m-d')));
        DB::commit();
        $this->ActivityLogsService->insert([
            'subject' => $main_sell,
            'title' => 'تم إضافة فاتورة بيع مرتجع',
            'description' => 'تم إضافة فاتورة بيع مرتجع بقيمة ' . $main_sell->final_price . ' لصالح ' . $main_sell->Contact?->name . 
                             ' في الفرع ' . $main_sell->Branch->name ? $main_sell->Branch->name : '' . 
                             ' رقم الفاتورة: ' . $main_sell->ref_no . ' ' .
                             ' بتاريخ ' . \Carbon\Carbon::parse($main_sell->transaction_date)->format('Y-m-d ') . 
                             '. تم تنفيذ المعاملة بواسطة المستخدم ' . auth()->user()->name . '.',
            'proccess_type' => 'sales',
            'user_id' => auth()->id(),
        ]);
        return redirect('dashboard/sells/sell-return')->with('success', 'success');
    }

    public function delete($sell_return_id){
        DB::beginTransaction();
        $sell_return = Transaction::find($sell_return_id);
        $this->SellReturnService->delete($sell_return);
        Notification::send(auth()->user(), new SalesNotification($sell_return, $sell_return->TransactionSellLines, 'delete', 'تم حذف فاتورة بيع مرتجع علي فاتورة بيع برقم ' . $sell_return->ref_no . ' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($sell_return->created_at)->format('Y-m-d')));
        DB::commit();
        $this->ActivityLogsService->insert([
            'subject' => $sell_return,
            'title' => 'تم حذف فاتورة بيع مرتجع',
            'description' => 'تم حذف فاتورة بيع مرتجع بقيمة ' . $sell_return->total . ' لصالح ' . $sell_return->contact->name . 
                             ' في الفرع ' . $sell_return->branch->name . 
                             ' رقم الفاتورة: ' . $sell_return->ref_no . ' ' .
                             ' بتاريخ ' . \Carbon\Carbon::parse($sell_return->transaction_date)->format('Y-m-d ') . 
                             '. تم تنفيذ المعاملة بواسطة المستخدم ' . auth()->user()->name . '.',
            'proccess_type' => 'sales',
            'user_id' => auth()->id(),
        ]);
        return redirect('dashboard/sells/sell-return')->with('success', 'success');
    }

    public function show($sell_id){
        $transaction = Transaction::find($sell_id);
        return [
            'title' => trans('admin.Show'),
            'body'  => view('Dashboard.sell-return.show')->with([
                'transaction' => $transaction,
            ])->render(),
        ];;
    }
}
