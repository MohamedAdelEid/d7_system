<div>
    <div class="top-cart-info">
        <a href="javascript:void(0);" class="top-cart-info-count" id="cart-count">{{ $totalQuantity }}  {{ trans('frontend.items') }}</a>
        <a href="javascript:void(0);" class="top-cart-info-value" id="cart-total">{{ trans('frontend.currency') }} {{ $total }}</a>
    </div>
    <i class="fa fa-shopping-cart"></i>

    <div class="top-cart-content-wrapper">
        <div class="top-cart-content">
            <ul class="scroller" style="height:auto; min-height:150px;" id="cart-items">
                @foreach ($items as $item)
                <li>
                    <a href="{{ route('show.product',$item->id) }}"><img src="{{$item->attributes['image']  ?? asset('assets/pages/img/products/model2.jpg') }}" alt="Rolex Classic Watch" width="37" height="34"></a>
                    <span class="cart-content-count">x {{ $item->quantity }}</span>
                    <strong><a href="{{ route('show.product',$item->id) }}">{{ $item->name }}</a></strong>
                    <em>{{ trans('frontend.currency') }} {{ number_format($item->price * $item->quantity, 2) }}</em>
                    <a href="javascript:;" class="del-goods" wire:click="remove('{{ $item->id }}')">&nbsp;</a>
                </li>
                @endforeach
                
            </ul>
            <div class="text-right">
                <button id="clear-cart" class="btn btn-danger" style="display: none;">Clear Cart</button>
                <a href="{{ route('cart.index') }}" class="btn btn-default">{{ trans('frontend.view_cart') }}</a>
            </div>
        </div>
    </div>

 
</div>
