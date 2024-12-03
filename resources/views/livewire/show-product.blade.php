<div>
    @if($isOutOfStock)
   
        <div class="product-quantity">
            <div class="input-group bootstrap-touchspin input-group-sm">
                <span class="input-group-btn">
                    <button class="btn quantity-down bootstrap-touchspin-down   {{ $isOutOfStock ? 'disabled' : '' }}"
                        wire:click="decrement" type="button" {{ $item && $item->quantity <= 1 ? 'disabled' : '' }}>
                        <i class="fa fa-angle-down"></i>
                    </button>
                </span>
                <input type="text" value="{{ $item->quantity ?? 1 }}" class="form-control" readonly>
                <span class="input-group-btn">
                    <button class="btn quantity-up bootstrap-touchspin-up   {{ $isOutOfStock ? 'disabled' : '' }}"
                        wire:click="increment" type="button">
                        <i class="fa fa-angle-up"></i>
                    </button>
                </span>
            </div>
        </div>
        <a href="javascript:;" class="btn btn-default add2cart   {{ $isOutOfStock ? 'disabled' : '' }}" wire:click.prevent="add">Add to cart</a>

    @else
    <div class="quantity">
            <input type="number" value="1" class="form-control" wire:model="quantity" min="1">
    </div>
    <a href="javascript:;" class="btn btn-default add2cart" wire:click.prevent="add"> {{ trans('frontend.add_to_cart') }}</a>
    @endif
</div>
