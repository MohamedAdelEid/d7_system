<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Darryldecode\Cart\Facades\CartFacade as Cart;

class CartCounter extends Component
{

    protected $listeners = ['cartUpdated' => 'render'];


    
    public function remove($itemId)
    {
        $ContactID = auth('contact')->user()->id;
        Cart::session($ContactID)->remove($itemId);
    }

    public function render()
    {
        if (Auth::guard('contact')->check()) {
        $ContactID = auth('contact')->user()->id;
        $totalQuantity = Cart::session($ContactID)->getContent()->count();
        $total = Cart::session($ContactID)->getTotal();
        $items = Cart::session($ContactID)->getContent();
        }else{
            $totalQuantity = Cart::getContent()->count();
            $total = Cart::getTotal();
            $items = Cart::getContent();
            
        }
    return view('livewire.cart-counter',compact('items','totalQuantity','total'));

    }
}
