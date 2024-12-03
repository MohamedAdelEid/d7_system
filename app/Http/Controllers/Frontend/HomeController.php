<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Product;
use App\Models\Category;
use App\Models\SiteSlider;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Support\Facades\Auth;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Symfony\Component\HttpFoundation\RequestMatcher\PortRequestMatcher;

class HomeController extends Controller
{
    public function index()
    {

        $categories = Category::with('subcategories')->whereNull('parent_id')->get();
        $sliders = SiteSlider::all();
        $latestProducts = Product::latest()->take(5)->get();
        $products = Product::all();
        $brands  = Brand::all();

        $setting = SiteSetting::get()->first();

        return view('Frontend.home.index', compact('categories', 'latestProducts', 'products','setting','sliders','brands'));
    }

    public function getProductsByCategory($id, Request $request)
    {
        $setting = SiteSetting::get()->first();

        $categories = Category::with('subcategories')->whereNull('parent_id')->get();
        $sliders = SiteSlider::all();
        $brands  = Brand::all();
        $category = Category::find($id);
        if ($category) {
            $products = Product::where('main_category_id', $id)->orWhere('category_id', $id)->get();
            return view('Frontend.category.index', compact('categories', 'products', 'category','setting','sliders','brands'));
        }

        return abort(404);
    }

    public function getProductsByBrand($id, Request $request)
    {
        $setting = SiteSetting::get()->first();

        $categories = Category::with('subcategories')->whereNull('parent_id')->get();
        $sliders = SiteSlider::all();
        $brands  = Brand::all();
        $category = Brand::find($id);
        if ($category) {
            $products = Product::where('brand_id', $id)->get();
            return view('Frontend.category.index', compact('categories', 'products', 'category','setting','sliders','brands'));
        }

        return abort(404);
    }


    public function showProduct($id){
        $setting = SiteSetting::get()->first();

        $product = Product::findOrFail($id);
        $categories = Category::with('subcategories')->whereNull('parent_id')->get();
        $sliders = SiteSlider::all();
        $item = Cart::get($id);
        $brands  = Brand::all();

        $branch = auth('contact')->check() ? auth('contact')->user()->getBranch() : null;
        $isOutOfStock = $branch ? $product->getStockByBranch($branch->id) <= 0 : true;

        return view('Frontend.category.show-product', compact('product','categories','item','setting','sliders','isOutOfStock','brands'));
        
    }

  
}
