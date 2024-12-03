<!-- BEGIN TOP BAR -->
<div class="pre-header">
    <div class="container">
        <div class="row">
            <!-- BEGIN TOP BAR LEFT PART -->
            <div class="col-md-6 col-sm-6 additional-shop-info">
                <ul class="list-unstyled list-inline">
                    <li><i class="fa fa-phone"></i><span>{{$setting->phone ?? '+201016454147'}}</span></li>

                    <!-- BEGIN LANGS -->
                    <li class="langs-block">
                        <a href="javascript:void(0);"
                            class="current">{{ LaravelLocalization::getSupportedLocales()[LaravelLocalization::getCurrentLocale()]['native'] }}</a>
                        <div class="langs-block-others-wrapper">
                            <div class="langs-block-others">
                                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                    @if ($localeCode !== LaravelLocalization::getCurrentLocale())
                                        <a
                                            href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                            {{ $properties['native'] }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </li>
                    <!-- END LANGS -->
                </ul>
            </div>
            <!-- END TOP BAR LEFT PART -->
            <!-- BEGIN TOP BAR MENU -->
            <div class="col-md-6 col-sm-6 additional-nav">
                <ul class="list-unstyled list-inline pull-right">
                    @if (Auth::guard('contact')->check())
                        <li><a href="{{ route('profile') }}">{{ trans('frontend.my_account') }}</a></li>
                        <li><a href="{{ route('cart.index') }}">{{ trans('frontend.shopping_list') }}</a></li>
                        <li>
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                {{ trans('frontend.logout') }}
                            </a>
                        </li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @else
                        <li><a href="{{ route('login') }}">{{ trans('frontend.login') }}</a></li>
                    @endif
                </ul>
            </div>
            <!-- END TOP BAR MENU -->
        </div>
    </div>
</div>
<!-- END TOP BAR -->

<!-- BEGIN HEADER -->
<div class="header">
    <div class="container">
        <a class="site-logo" href="{{ route('index') }}">
            <img src="{{ $setting && $setting->getMedia('logo')->first() ? $setting->getMedia('logo')->first()->getUrl() : asset('assets/corporate/img/logos/logo-shop-red.png') }}" 
            alt="Metronic Shop UI" 
            style="width: auto; height: 50px; max-width: 100%; object-fit: contain;">

        </a>

        <a href="javascript:void(0);" class="mobi-toggler"><i class="fa fa-bars"></i></a>

      <!-- BEGIN CART -->
      <div class="top-cart-block" id="top-cart-block">
        @livewire('cart-counter')
    </div>
    <!-- END CART -->

    <!-- BEGIN NAVIGATION -->
    <div class="header-navigation">
        <ul>
        
            @foreach ($categories as $category)
            <li class="dropdown">
                @if ($category->subcategories->isNotEmpty())
                    <a href="{{ route('category', $category->id) }}">
                        {{ $category->name }}
                    </a>
                    <ul class="dropdown-menu">
                        @foreach ($category->subcategories as $subcategory)
                            <li><a href="{{ route('category', $subcategory->id) }}">{{ $subcategory->name }}</a></li>
                        @endforeach
                    </ul>
                @else
                    <a href="{{ route('category', $category->id) }}">{{ $category->name }}</a>
                @endif
            </li>
        @endforeach
        
        
        </ul>
    </div>
</div>
</div>
<!-- Header END -->
