<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Auth;
use Darryldecode\Cart\Facades\CartFacade as Cart;

class CartItems extends Component
{

    public function increment($itemId)
    {
        // Increase quantity directly
        $ContactID = auth('contact')->user()->id;
        Cart::session($ContactID)->update($itemId, [
            'quantity' => 1, // Add 1 to the current quantity
        ]);

        $this->dispatch('cartUpdated'); // Trigger an event for the frontend
    }

    public function decrement($itemId)
    {
        $ContactID = auth('contact')->user()->id;

        $item = Cart::session($ContactID)->get($itemId);

        if ($item && $item->quantity > 1) {
            // Reduce quantity by 1
            $ContactID = auth('contact')->user()->id;
            Cart::session($ContactID)->update($itemId, [
                'quantity' => -1, // Subtract 1 from the current quantity
            ]);

            $this->dispatch('cartUpdated'); // Trigger an event for the frontend
        } else {
            $this->remove($itemId); // Remove the item if quantity reaches 0
        }
    }

    public function remove($itemId)
    {
        $ContactID = auth('contact')->user()->id;
        Cart::session($ContactID)->remove($itemId);
        $this->dispatch('cartUpdated');
    }


    public function render()
    {
        if (Auth::guard('contact')->check()) {
        $ContactID = auth('contact')->user()->id;
        $items = Cart::session($ContactID)->getContent();
        }else{
            $items = Cart::getContent();

        }
    $setting = SiteSetting::get()->first() ;

        return view('livewire.cart-items',compact('items','setting'));
    }
}
