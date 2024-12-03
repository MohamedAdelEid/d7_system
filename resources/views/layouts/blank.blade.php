@yield('style')

@include('Dashboard.includes.header')


@yield('content')

@include('Dashboard.includes.footer')

@yield('script')

@include('Dashboard.includes.ajax')

@include('Dashboard.partials._session')
@include('Dashboard.partials.popup')