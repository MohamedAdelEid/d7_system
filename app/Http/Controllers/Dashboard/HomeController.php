<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\Branch;
use App\Models\Contact;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductBranchDetails;
use Illuminate\Support\Facades\Storage;
use ArielMejiaDev\LarapexCharts\LarapexChart;


class HomeController extends Controller
{
    public function index(Request $request)
    {

        $branches = Branch::get();
        # Make THE DEFAULT DATE IS TODAY
        $date_from = $request->date_from ?? Carbon::now()->format('Y-m-d');
        $date_to = $request->date_to ?? Carbon::now()->format('Y-m-d');
        $date_from = $date_from . ' 00:00:00';
        $date_to = $date_to . ' 23:59:59';

        # Total Sales
        $total_sales_query = Transaction::query();
        if (isset($request->branch_id) && $request->branch_id) {
            $total_sales_query->where('branch_id', $request->branch_id);
        }
        if ($date_from && $date_to) {
            $total_sales_query->whereBetween('transaction_date', [$date_from, $date_to]);
        }
        $total_sales = $total_sales_query->where('type', 'sell')->sum('final_price');

        # Total Sales Returns


        $total_sales_returns_query = Transaction::query();
        if (isset($request->branch_id) && $request->branch_id) {
            $total_sales_returns_query->where('branch_id', $request->branch_id);
        }
        if ($date_from && $date_to) {
            $total_sales_returns_query->whereBetween('transaction_date', [$date_from, $date_to]);
        }
        $total_sales_returns = $total_sales_returns_query->where('type', 'sell_return')->sum('final_price');




        # Total Unpaid Sales
        $total_unpaid_sales_query = Transaction::query();
        if (isset($request->branch_id) && $request->branch_id) {
            $total_unpaid_sales_query->where('branch_id', $request->branch_id);
        }
        if ($date_from && $date_to) {
            $total_unpaid_sales_query->whereBetween('transaction_date', [$date_from, $date_to]);
        }
        $total_unpaid_sales = $total_unpaid_sales_query->where('type', 'sell')->where('payment_status', 'due')->sum('final_price');



        # Total Paid Sales
        $total_paid_sales_query = Transaction::query();
        if (isset($request->branch_id) && $request->branch_id) {
            $total_paid_sales_query->where('branch_id', $request->branch_id);
        }
        if ($date_from && $date_to) {
            $total_paid_sales_query->whereBetween('transaction_date', [$date_from, $date_to]);
        }
        $total_paid_sales = $total_paid_sales_query->where('type', 'sell')->where('payment_status', 'final')->sum('final_price');



        # Total Purchase
        $totla_purchase_query = Transaction::query();

        if (isset($request->branch_id) && $request->branch_id) {
            $totla_purchase_query->where('branch_id', $request->branch_id);
        }
        if ($date_from && $date_to) {
            $totla_purchase_query->whereBetween('transaction_date', [$date_from, $date_to]);
        }
        $totla_purchase = $totla_purchase_query->where('type', 'purchase')->sum('final_price');

        $total_paid_purchase_query = Transaction::query();
        if (isset($request->branch_id) && $request->branch_id) {
            $total_paid_purchase_query->where('branch_id', $request->branch_id);
        }
        if ($date_from && $date_to) {
            $total_paid_purchase_query->whereBetween('transaction_date', [$date_from, $date_to]);
        }
        $total_paid_purchase = $total_paid_purchase_query->where('type', 'purchase')->where('payment_status', 'final')->sum('final_price');



        # Total Purchase Returns
        $totla_purchase_returns_query = Transaction::query();
        if (isset($request->branch_id) && $request->branch_id) {
            $totla_purchase_returns_query->where('branch_id', $request->branch_id);
        }
        if ($date_from && $date_to) {
            $totla_purchase_returns_query->whereBetween('transaction_date', [$date_from, $date_to]);
        }
        $totla_purchase_returns = $totla_purchase_returns_query->where('type', 'purchase_return')->sum('final_price');

        # Total Unpaid Purchase
        $totla_unpaid_purchase_query = Transaction::query();
        if (isset($request->branch_id) && $request->branch_id) {
            $totla_unpaid_purchase_query->where('branch_id', $request->branch_id);
        }
        if ($date_from && $date_to) {
            $totla_unpaid_purchase_query->whereBetween('transaction_date', [$date_from, $date_to]);
        }
        $totla_unpaid_purchase = $totla_unpaid_purchase_query
            ->where('type', 'purchase')->where('payment_status', 'due')->sum('final_price');
        
        
        # Total Partial Purchase
        $total_partial_purchase = Transaction::query();
        if (isset($request->branch_id) && $request->branch_id) {
            $total_partial_purchase->where('branch_id', $request->branch_id);
        }
        if ($date_from && $date_to) {
            $total_partial_purchase->whereBetween('transaction_date', [$date_from, $date_to]);
        }
        $total_partial_purchase = $total_partial_purchase->where('type', 'purchase')->where('payment_status', 'partial')->sum('final_price');

        # Total Partial Sell
        $total_partial_sell_query = Transaction::query();
        if (isset($request->branch_id) && $request->branch_id) {
            $total_partial_sell_query->where('branch_id', $request->branch_id);
        }
        if ($date_from && $date_to) {
            $total_partial_sell_query->whereBetween('transaction_date', [$date_from, $date_to]);
        }
        $total_partial_sell = $total_partial_sell_query->where('type', 'sell')->where('payment_status', 'partial')->sum('final_price');

        # Total Expenses
        $total_expenses_query = Expense::query();
        if (isset($request->branch_id) && $request->branch_id) {
            $total_expenses_query->where('branch_id', $request->branch_id);
        }
        if ($date_from && $date_to) {
            $total_expenses_query->whereBetween('created_at', [$date_from, $date_to]);
        }
        $total_expenses = $total_expenses_query->sum('amount');

        $net_profit = $total_sales - ($totla_purchase + $total_expenses);
        $total_profit =   $total_sales - $totla_purchase;



        $userAuth = \Auth::user();

        $uesrAuthBranchesIds = $userAuth->Branches->pluck('id');

        $products = ProductBranchDetails::query();
        if (!$userAuth->super) {

            $products->whereIn('branch_id', $uesrAuthBranchesIds);
        }

        $products = $products->whereHas('Product', function ($query) {
            $query->whereColumn('product_branch_details.qty_available', '<=', 'products.quantity_alert');
        })->with('Product')->get();
        // start transactions'chart

        $dates = [];
        $totals = [];
        // Generate the last 30 days
        $last30Days = collect();
        for ($i = 0; $i < 30; $i++) {
            $last30Days->push(Carbon::now()->subDays($i)->format('Y-m-d'));
        }
        // Reverse the collection so it starts from the oldest day
        $last30Days = $last30Days->reverse();

        // Get transactions for the last 30 days where type = sell
        $transactions = Transaction::where('type', 'sell')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('Y-m-d'); // Group by date
            });


        // Loop through the last 30 days and assign total or 0
        foreach ($last30Days as $day) {
            $dates[] = $day;
            // If a transaction exists for the day, sum the totals, otherwise set to 0
            if (isset($transactions[$day])) {
                $totals[] = $transactions[$day]->sum('final_price');
            } else {
                $totals[] = 0;
            }
        }
        // Generate the chart
        $chart = (new LarapexChart)->lineChart()
            ->setTitle('Sales for the Last 30 Days')
            ->setXAxis($dates)
            ->setDataset([
                [
                    'name' => 'Total Sales',
                    'data' => $totals
                ]
            ]);



        return view('Dashboard.home', compact(
            'total_sales',
            'total_sales_returns',
            'total_unpaid_sales',
            'totla_purchase',
            'total_paid_sales',
            'total_paid_purchase',
            'totla_purchase_returns',
            'products',
            'totla_unpaid_purchase',
            'total_expenses',
            'total_profit',
            'net_profit',
            'chart',
            'branches',
            'date_from',
            'date_to',
            'total_partial_purchase',
            'total_partial_sell'
        ));
    }
    public function markAsRead(Request $request)
    {
        $notification = auth()->user()->notifications()->find($request->notification_id);
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $count = auth()->user()->unreadNotifications->count();
        return response()->json(['count' => $count]);
    }
}
