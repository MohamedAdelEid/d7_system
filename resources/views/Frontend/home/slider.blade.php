@section('header')

@if($sliders->isNotEmpty())
<!-- BEGIN SLIDER -->
<div class="page-slider margin-bottom-35">
    <div id="carousel-example-generic" class="carousel slide carousel-slider">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            @foreach($sliders as $index => $slider)
                <li data-target="#carousel-example-generic" data-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}"></li>
            @endforeach
        </ol>

        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox">
            @foreach($sliders as $index => $slider)
                <div class="item carousel-item-{{ $index + 4 }} {{ $index === 0 ? 'active' : '' }}">
                    <div class="container-fluid p-0" style="padding: 0px">
                        <div class="carousel-position-four text-center" style="padding: 20px">
                            <!-- Dynamic Title -->
                            <h2 class="margin-bottom-20 animate-delay carousel-title-v3 border-bottom-title text-uppercase" data-animation="animated fadeInDown">
                                {{ $slider->title ?? 'Default Title' }}
                            </h2>

                            <!-- Dynamic Subtitle -->
                            <p class="carousel-subtitle-v2" data-animation="animated fadeInUp">
                                {{ $slider->sub_title ?? 'Default Subtitle' }}
                            </p>

                            <!-- Dynamic Button (if applicable) -->
                            @if(!empty($slider->button_text) && !empty($slider->button_link))
                                <a class="carousel-btn" href="{{ $slider->button_link }}" data-animation="animated fadeInUp">
                                    {{ $slider->button_text }}
                                </a>
                            @endif
                        </div>

                        <!-- Dynamic Image -->
                        <img class="carousel-image animate-delay"
                            src="{{ $slider && method_exists($slider, 'getMedia') && $slider->getMedia('sliders')->first() 
                                ? $slider->getMedia('sliders')->first()->getUrl() 
                                : asset('assets/pages/img/products/model2.jpg') }}"
                            alt="{{ $slider->title ?? 'Default Title' }}"
                            data-animation="animated zoomIn" style="height: 500px">
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Controls -->
        <a class="left carousel-control carousel-control-shop" href="#carousel-example-generic" role="button" data-slide="prev">
            <i class="fa fa-angle-left" aria-hidden="true"></i>
        </a>
        <a class="right carousel-control carousel-control-shop" href="#carousel-example-generic" role="button" data-slide="next">
            <i class="fa fa-angle-right" aria-hidden="true"></i>
        </a>
    </div>
</div>
<!-- END SLIDER -->
@else
<p>No sliders available.</p>
@endif

@endsection
