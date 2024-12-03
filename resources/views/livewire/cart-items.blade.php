<div>
    <div class="goods-page">
        <div class="goods-data clearfix">
            <div class="table-wrapper-responsive">
                <table summary="Shopping cart">
                    <tbody>
                        <tr>
                            <th class="goods-page-image">{{ trans('frontend.image') }}</th>
                            <th class="goods-page-description">{{ trans('frontend.description') }}</th>
                            <th class="goods-page-quantity">{{ trans('frontend.quantity') }}</th>
                            <th class="goods-page-price">{{ trans('frontend.unit_price') }}</th>
                            <th class="goods-page-total" colspan="2">{{ trans('frontend.total') }}</th>
                        </tr>
                        
                    <tbody id="cart-items">
                        @foreach ($items as $item)
                            <input type="hidden" name="item_ids[]" value="{{ $item->id }}">
                            <tr class="cart-item" data-product-id="{{ $item->id }}">
                                <td class="goods-page-image">
                                    <a href="javascript:;"><img
                                            src="{{ asset('assets') }}/pages/img/products/model2.jpg"
                                            alt="{{ $item->name }}"></a>
                                </td>
                                <td class="goods-page-description">
                                    <h3><a href="javascript:;">{{ $item->name }}</a></h3>
                                    <p><strong>{{ trans('frontend.item') }} {{ $item->id }}</strong></p>
                                </td>
                                <td class="goods-page-quantity">
                                    <div class="product-quantity">
                                        <div class="input-group bootstrap-touchspin input-group-sm">
                                            <span class="input-group-btn">
                                                <button class="btn quantity-down bootstrap-touchspin-down"
                                                    wire:click="decrement('{{ $item->id }}')" type="button">
                                                    <i class="fa fa-angle-down"></i>
                                                </button>
                                             </span>
                                             <input type="text" value="{{ $item->quantity }}" class="form-control" readonly> 
                                             <span class="input-group-btn">
                                                <button class="btn quantity-up bootstrap-touchspin-up" wire:click="increment('{{ $item->id }}')" type="button">
                                                    <i class="fa fa-angle-up"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>



                                </td>
                                <td class="goods-page-price">
                                    <strong><span>{{ trans('frontend.currency') }}</span>{{ number_format($item->price, 2) }}</strong>
                                </td>
                                <td class="goods-page-total">
                                    <strong><span>{{ trans('frontend.currency') }}</span><span
                                            class="total-price">{{ number_format($item->quantity * $item->price, 2) }}</span></strong>
                                </td>
                                <td class="del-goods-col">
                                    <a class="del-goods" href="javascript:;" wire:click="remove('{{ $item->id }}')">&nbsp;</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    </tbody>
                </table>
                

            </div>

            <!-- Summary Section -->
            <div class="shopping-total">
                <ul>
                    <li>
                        <em>{{ trans('frontend.sub_total') }}</em>
                        <strong class="price">
                            <span>{{ trans('frontend.currency') }}</span>
                            <span id="subtotal-price">{{ number_format(Cart::getSubTotal(), 2) }}</span>
                        </strong>
                    </li>
                    <li class="shopping-total-price">
                        <em>{{ trans('frontend.total') }}</em>
                        <strong class="price">
                            <span>{{ trans('frontend.currency') }}</span>
                            <span id="total-price">
                                {{ number_format(Cart::getTotal(), 2) }}
                            </span>
                        </strong>
                    </li>
                    
                </ul>
            </div>
        </div>
        <a href="{{ route('index') }}" class="btn btn-default" type="button">
            {{ trans('frontend.continue_shopping') }} <i class="fa fa-shopping-cart"></i>
        </a>
        <button class="btn btn-primary" type="submit">
            {{ trans('frontend.checkout') }} <i class="fa fa-check"></i>
        </button>
        
    </div>
</div>
