<!DOCTYPE html>
<html lang="en">
    <head>
        @include('Frontend.includes.meta')
        @include('Frontend.includes.style')
        @yield('style')
    </head>
    <body class="ecommerce">
        @include('Frontend.includes.header')
        @yield('header')
        <div class="main">
            <div class="container">
                @yield('content')
                @include('Frontend.includes.product-pop-up')
            </div>
        </div>
        @include('Frontend.includes.footer')
        @yield('script')
        @include('Frontend.includes.script')
    </body>
</html>