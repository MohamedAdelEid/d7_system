<div class="brand-section">
    <div class="content-wrapper">
        <h2 class="margin-left-20">{{ trans('admin.brands') }}</h2>

        <div class="brand-list">
            @foreach ($brands as $brand)
                <div class="brand-box">
                    <a href="{{ route('brand',$brand->id) }}">
                        <img src="{{ $brand->getFirstMediaUrl('brands') ?: asset('assets/corporate/img/logos/logo-shop-red.png') }}" alt="Brand Image">
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- BEGIN PRE-FOOTER -->
<div class="pre-footer">
    <div class="container">
        <div class="row">
            <!-- BEGIN BOTTOM ABOUT BLOCK -->
            <div class="col-md-6 col-sm-6 pre-footer-col">
                <h2>{{ __('frontend.about_us') }}</h2>
                <p>
                    {{ $setting->about_us ?? __('frontend.default_about_us') }}
                </p>
                
            </div>
            <!-- END BOTTOM ABOUT BLOCK -->



            <!-- BEGIN BOTTOM CONTACTS -->
            <div class="col-md-6 col-sm-6 pre-footer-col">
                <h2>{{ __('frontend.contact_us') }}</h2>
                <address class="margin-bottom-40">
                    {{ $setting->phone ?? __('frontend.default_phone') }}<br>
                    {{ $setting->address ?? __('frontend.default_address') }}<br>
                    Email: <a href="mailto:{{ $setting->email ?? __('frontend.default_email') }}">{{ $setting->email ?? __('frontend.default_email') }}</a><br>
                </address>
                
            </div>
            <!-- END BOTTOM CONTACTS -->
        </div>
        <hr>
        <div class="row">
            <!-- BEGIN SOCIAL ICONS -->
            <div class="col-md-6 col-sm-6">
                <ul class="social-icons">
                    <li><a class="facebook" data-original-title="facebook" href="{{$setting->facebook ?? 'https://www.facebook.com/schemecode'}}"></a></li>
                    <li><a class="twitter" data-original-title="twitter" href="{{$setting->twitter ?? 'https://www.linkedin.com/company/scheme-code/mycompany/'}}"></a></li>
                    <li><a class="linkedin" data-original-title="linkedin" href="{{$setting->linkedin ?? 'https://www.linkedin.com/company/scheme-code/mycompany/'}}"></a></li>
                    <li><a class="instagram" data-original-title="instagram" href="{{$setting->instagram ?? 'https://www.instagram.com/schemecode/?hl=en'}}"></a></li>
                </ul>
            </div>
            <!-- END SOCIAL ICONS -->

        </div>
    </div>
</div>
<!-- END PRE-FOOTER -->

<!-- BEGIN FOOTER -->
<div class="footer">
    <div class="container">
        <div class="row">
            <!-- BEGIN COPYRIGHT -->
            <div class="col-md-6 col-sm-6 padding-top-10">
                2024 © schemecode. {{ __('frontend.all_rights_reserved') }}
            </div>
            
            <!-- END COPYRIGHT -->

            <!-- BEGIN POWERED -->
            <div class="col-md-6 col-sm-6 text-right">
                <p class="powered">{{ __('frontend.powered_by') }}: <a href="https://schemecode.com//">schemecode.com</a></p>
            </div>
            
            <!-- END POWERED -->
        </div>
    </div>
</div>
<!-- END FOOTER -->
