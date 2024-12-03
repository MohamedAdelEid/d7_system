@if ($products->count() > 0)
    <div class="col-md-12 col-sm-12">
        <div class="row list-view-sorting clearfix">
            <div class="col-md-2 col-sm-2 list-view">
                <a href="javascript:;"><i class="fa fa-th-large"></i></a>
                <a href="javascript:;"><i class="fa fa-th-list"></i></a>
            </div>
   
        </div>

        <!-- BEGIN PRODUCT LIST -->
        <div class="row product-list">
            @foreach ($products as $product)
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div>
                        <div class="product-item ">
                            <div class="pi-img-wrapper">
                                <img src="{{$product->getImage() ?? asset('assets/pages/img/products/model2.jpg') }}" class="img-responsive"
                                    alt="Berry Lace Dress" style="height: 150px;width: 100%; object-fit: cover;">
                                <div>
                                    <a href="{{$product->getImage() ?? asset('assets/pages/img/products/model2.jpg') }}"
                                        class="btn btn-default fancybox-button">{{ trans('frontend.zoom') }}</a>
                                    <a href="{{ route('show.product', $product->id) }}"
                                        class="btn btn-default fancybox-fast-view view-product"
                                        data-id="{{ $product->id }}">{{ trans('frontend.view') }}</a>
                                </div>
                            </div>
                            <h3><a href="{{ route('show.product', $product->id) }}">{{ $product->name }}</a> </h3>
                            <div class="pi-price"> <span>{{ trans('frontend.currency') }} </span>{{ $product->getSellPrice() }} / <span
                                    class="badge ">
                                    {{ $product->getMainUnitName($product->unit_id) }}</span></div>
                                    @livewire('add-to-cart', ['productId' => $product->id], key($product->id))
                        </div>
                    </div>
                </div>
            @endforeach
        </div>


    </div>
@else
    <div class="alert-warning p-20 text-center h3">

        لا يوجد منتجات بعد
    </div>
@endif
