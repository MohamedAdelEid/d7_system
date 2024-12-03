@extends('layouts.frontend')

@section('title', trans('frontend.home'))

@section('content')
    <div class="row">
        <div class="product-page">
            <div class="row">
                <!-- Product Image Section -->
                <div class="col-md-6 col-sm-6">
                    <div class="product-main-image" style="position: relative; overflow: hidden;">
                        <img src="{{ $product->getImage() ?? asset('assets/pages/img/products/model2.jpg') }}"
                            alt="{{ $product->name }}" class="img-responsive"
                            data-bigimgsrc="{{ $product->getImage() ?? asset('assets/pages/img/products/model2.jpg') }}">
                        <img src="{{ $product->getImage() ?? asset('assets/pages/img/products/model2.jpg') }}"
                            class="zoomImg"
                            style="position: absolute; top: 0px; left: 0px; opacity: 0; width: 600px; height: 800px; border: none; max-width: none;">
                    </div>
                </div>

                <!-- Product Details Section -->
                <div class="col-md-6 col-sm-6">
                    <h1>{{ $product->name }}</h1>
                    <div class="price-availability-block clearfix">
                        <div class="price">
                            <strong><span>{{ trans('frontend.currency') }}</span>{{ $product->getSellPrice() }}</strong>
                        </div>
                        <div class="availability">
                            {{ trans('frontend.availability') }}: <strong>
                                @if ($isOutOfStock)
                                    <span class="text-danger">{{ trans('frontend.out_of_stock') }}</span>
                                @else
                                    <span class="text-success">{{ trans('frontend.in_stock') }}</span>
                                @endif
                            </strong>
                        </div>
                    </div>

                    <!-- Product Short Description -->
                    <div class="description text-right">
                        <h4>{{ trans('frontend.description') }}</h4>
                        <p>{{ $product->description }}</p>
                    </div>

                    <!-- Product Additional Information -->
                    <div class="additional-info mt-4 text-right">
                        <h4>{{ trans('frontend.additional_info') }}</h4>
                        <ul class="list-group ">
                            <li class="list-group-item">
                                <strong>{{ trans('frontend.unit') }}:</strong> {{ $product->MainUnit->actual_name ?? '-' }}
                            </li>
                            <li class="list-group-item">
                                <strong>{{ trans('frontend.brand') }}:</strong> {{ $product->brand->name ?? '-' }}
                            </li>
                            <li class="list-group-item">
                                <strong>{{ trans('frontend.main_category') }}:</strong> {{ $product->Category->name ?? '-' }}
                            </li>
                        </ul>
                    </div>

                    <!-- Add to Cart Section -->
                    <div class="product-page-cart mt-4">
                        @livewire('show-product', ['productId' => $product->id], key($product->id))
                    </div>
                </div>
            </div>

           
        </div>
    </div>
@endsection

@section('script')
@endsection
