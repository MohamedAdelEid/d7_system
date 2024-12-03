<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Darryldecode\Cart\Facades\CartFacade as Cart;

class ShowProduct extends Component
{
    public $productId;
    public $isOutOfStock = false;
    public $quantity = 1; // القيمة الافتراضية للكمية

    public function mount($productId)
    {
        $this->productId = $productId;

        // Check stock availability for authenticated users
        if (Auth::guard('contact')->check()) {
            $branch = auth('contact')->user()->getBranch();
            if ($branch) {
                $product = Product::find($this->productId);
                $this->isOutOfStock = $product && $product->getStockByBranch($branch->id) <= 0;
            } else {
                $this->isOutOfStock = true;
            }
        } else {
            $this->isOutOfStock = true; // Unauthenticated users cannot access stock
        }
    }

    public function add()
    {
        if ($this->isOutOfStock) {
            return; // Do nothing if out of stock
        }

        if (Auth::guard('contact')->check()) {
            $product = Product::findOrFail($this->productId);

            $contactID = auth('contact')->user()->id;

            Cart::session($contactID)->add([
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->getSellPrice(),
                'quantity' => $this->quantity, // استخدام الكمية المدخلة
                'attributes' => [
                    'image' => $product->getImage(),
                ],
            ]);
            $this->quantity = 1;
            $this->dispatch('cartUpdated');
        } else {
            return redirect()->route('login');
        }
    }

    public function increment()
    {
        if ($this->isOutOfStock) {
            return; // Do nothing if out of stock
        }

        if (Auth::guard('contact')->check()) {
            $contactID = auth('contact')->user()->id;

            $item = Cart::session($contactID)->get($this->productId);

            if ($item) {
                Cart::session($contactID)->update($this->productId, [
                    'quantity' => $this->quantity + 1, // زيادة الكمية حسب المدخل
                ]);

                $this->dispatch('cartUpdated');
            } else {
                $this->add(); // إضافة المنتج إذا لم يكن موجوداً في السلة
            }
        } else {
            return redirect()->route('login');
        }
    }

    public function decrement()
    {
        if ($this->isOutOfStock) {
            return; // Do nothing if out of stock
        }

        if (Auth::guard('contact')->check()) {
            $contactID = auth('contact')->user()->id;

            $item = Cart::session($contactID)->get($this->productId);

            if ($item && $this->quantity > 1) {
                Cart::session($contactID)->update($this->productId, [
                    'quantity' => $this->quantity - 1, // تقليل الكمية حسب المدخل
                ]);

                $this->dispatch('cartUpdated');
            } else {
                $this->remove();
            }
        } else {
            return redirect()->route('login');
        }
    }

    public function remove()
    {
        if (Auth::guard('contact')->check()) {
            $contactID = auth('contact')->user()->id;

            Cart::session($contactID)->remove($this->productId);
            $this->dispatch('cartUpdated');
        }
    }

    public function render()
    {
        $contactID = Auth::guard('contact')->check() ? auth('contact')->user()->id : null;
        $item = $contactID ? Cart::session($contactID)->get($this->productId) : null;

        return view('livewire.show-product', [
            'item' => $item,
        ]);
    }
}
