<!-- BEGIN SALE PRODUCT & NEW ARRIVALS -->
<div class="row owl-theme margin-bottom-40" >
    <h2 class="margin-left-20">{{ trans('frontend.new_arrivals') }}</h2>
    
    <!-- BEGIN SALE PRODUCT -->
    @foreach ($latestProducts as $product)
            <div class="col-md-3 col-sm-6 col-xs-12 " >
                <div class="product-item">
                <div class="pi-img-wrapper">
                    <img src="{{$product->getImage() ?? asset('assets/pages/img/products/model2.jpg') }}" class="img-responsive"
                        alt="Berry Lace Dress" style="height: 200px;width:100%; object-fit: cover;">
                    <div>
                        <a href="{{$product->getImage() ?? asset('assets/pages/img/products/model2.jpg') }}"
                            class="btn btn-default fancybox-button">{{ trans('frontend.zoom') }}</a>
                        <a href="{{ route('show.product',$product->id) }}" class="btn btn-default fancybox-fast-view view-product"
                            data-id="{{ $product->id }}">{{ trans('frontend.view') }}</a>
                    </div>
                </div>
                <h3><a href="{{ route('show.product',$product->id) }}">{{ $product->name }}</a> </h3>
                <div class="pi-price"> <span>{{ trans('frontend.currency') }} </span>{{ $product->getSellPrice() }} / <span class="badge ">
                        {{ $product->getMainUnitName($product->unit_id) }}</span></div>
                        @livewire('add-to-cart', ['productId' => $product->id])
                <div class="sticker sticker-new"></div>
            </div>
            </div>
            @endforeach

    <!-- END SALE PRODUCT -->
</div>
<!-- END SALE PRODUCT & NEW ARRIVALS -->
