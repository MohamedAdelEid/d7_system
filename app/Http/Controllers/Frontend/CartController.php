<?php

namespace App\Http\Controllers\Frontend;

use App\Models\User;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Branch;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use App\Services\SellService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Darryldecode\Cart\Facades\CartFacade as Cart;

class CartController extends Controller
{
    public $SellService;
    protected $PaymentTransactionService;
    public $TransactionService;
    protected $ActivityLogsService;
    public function __construct(SellService $SellService)
    {
        $this->SellService = $SellService;
    }


    public function index()
    {
        $setting = SiteSetting::get()->first();
        $brands  = Brand::all();

        $categories = Category::with('subcategories')->whereNull('parent_id')->get();
        return view('Frontend.cart.all', compact('categories', 'setting','brands'));
    }


    public function checkOut(Request $request)
    {
        $contact = auth('contact')->user();
        $branch =  DB::table('branchs')->where('governorate_id', $contact->governorate_id)->first();
        $items = Cart::session($contact->id)->getContent();
        $setting = SiteSetting::get()->first();

        $subtotal = $items->sum(fn($item) => $item->quantity * $item->price);
        $tax = (($setting->tax ?? 14) / 100) * $subtotal;
        $total = $subtotal + $tax;


        $products = []; // مصفوفة لتخزين البيانات بالشكل المطلوب

        foreach ($items as $item) {
            // حساب الكمية الرئيسية إن كانت مطلوبة
            $mainQuantity = $item->quantity * ($item->attributes->unit_multiplier ?? 1);

            $products[] = [
                'product_id'  => $item->id,
                'quantity'    => $item->quantity,
                'unit_price'  => $item->price,
                'total'       => $item->quantity * $item->price,

            ];
        }
        DB::beginTransaction();

        try {



            // Prepare the data for creating a sale
            $data = [
                'branch_id' => $branch->id,
                'contact_id' =>  $contact->id,
                'payment_type' => "credit",
                'payment_status' => "due",
                'status' => 'final',
                'transaction_from' => 'site',
                'account_id' => $branch->credit_account_id,
                'discount_value' => null,
                'discount_type' => null,
                'amount' => $total,
            ];

            $transaction = $this->SellService->CreateSell($data, $products, $request);
            Cart::clear();


            DB::commit();


            return  redirect()->route('profile')->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
