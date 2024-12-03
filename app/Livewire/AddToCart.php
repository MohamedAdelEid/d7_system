<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Darryldecode\Cart\Facades\CartFacade as Cart;

class AddToCart extends Component
{
    public $productId;
    public $isOutOfStock = false;

    public function mount($productId)
    {
        $this->productId = $productId;

        // Check stock availability
        if (Auth::guard('contact')->check()) {
            $branch = auth('contact')->user()->getBranch();
            if ($branch) {
                $product = Product::find($this->productId);
                $this->isOutOfStock = $product && $product->getStockByBranch($branch->id) <= 0;
            }
        } else {
            $this->isOutOfStock = true; // Consider as out of stock if user is not authenticated
        }
    }

    public function add()
    {
        if (Auth::guard('contact')->check()) {
            $product = Product::findOrFail($this->productId);
            $contactID = auth('contact')->user()->id;

            Cart::session($contactID)->add([
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->getSellPrice(),
                'quantity' => 1,
                'attributes' => [
                    'image' => $product->getImage(),
                ],
            ]);

            $this->dispatch('cartUpdated');
        } else {
            return redirect()->route('login');
        }
    }

    public function render()
    {
        return view('livewire.add-to-cart');
    }
}
