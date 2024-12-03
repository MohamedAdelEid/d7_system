<?php

namespace App\Http\Controllers\Api\contact;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // get all product
    public function allProducts() {
        // get all products
        $products = Product::all();
        // check set or no
        if (!$products) {
            return $this->failed('Unauthorized: No products available', 401, 'E01');
        }
        // return data
        $response = ['products'  => $products];
        return $this->success(trans('api.success'),200, 'data', $response);
    }
    // get  product by brand
    public function getProductsByBrand($id) {
        // get all products
        $products = Product::where('brand_id', $id)->get();
        // check set or no
        if (!$products) {
            return $this->failed('Unauthorized:  products not available', 401, 'E01');
        }
        // return data
        $response = ['products'  => $products];
        return $this->success(trans('api.success'),200, 'data', $response);
    }
    // get  product by branch
    public function getProductsByBranch($id) {
        // get all products
        $products = Product::whereHas('ProductBranchDetails', function($query) use ($id) {
            $query->where('branch_id', $id);
        })->with(['ProductBranchDetails' => function($query) use ($id) {
            // Only get the ProductBranchDetails for the specific branch
            $query->where('branch_id', $id);
        }])->get();
        
        // check set or no
        if (!$products) {
            return $this->failed('Unauthorized:  products not available', 401, 'E01');
        }
        // return data
        $response = ['products'  => $products];
        return $this->success(trans('api.success'),200, 'data', $response);
    }
    // get single product
    public function showProduct($id) {
        // get all products
        $product = Product::find($id);
        // check set or no
        if (!$product) {
            return $this->failed('Unauthorized:  product not available', 401, 'E01');
        }
        // return data
        $response = ['product'  => $product];
        return $this->success(trans('api.success'),200, 'data', $response);
    }
}
