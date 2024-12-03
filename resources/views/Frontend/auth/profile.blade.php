@extends('layouts.frontend')

@section('title', trans('frontend.profile'))

@section('content')
    <ul class="breadcrumb">
        <li><a href="{{ route('index') }}">{{ trans('frontend.home') }}</a></li>
        <li class="active">الصفحة الشخصية</li>
    </ul>
    <div class="row margin-bottom-40">
        <!-- BEGIN SIDEBAR -->
        <div class="sidebar col-md-3 col-sm-3">
            <ul class="list-group margin-bottom-25 sidebar-menu">
                <li class="list-group-item clearfix  {{request()->routeIs('profile') ? 'active':''}}"><a href="{{ route('profile') }}"><i class="fa fa-angle-right"></i>{{ __('frontend.profile') }}</a></li>
                <li class="list-group-item clearfix  {{request()->routeIs('profile.transaction') ? 'active':''}}"><a href="{{ route('profile.transaction') }}"><i class="fa fa-angle-right"></i>{{ __('frontend.my_orders') }}</a></li>
                <li class="list-group-item clearfix {{request()->routeIs('profile.edit') ? 'active':''}}"><a href="{{ route('profile.edit') }}"><i class="fa fa-angle-right"></i>{{ __('frontend.edit_profile') }}</a></li>
                <li class="list-group-item clearfix {{request()->routeIs('profile.password') ? 'active':''}}"><a href="{{ route('profile.password') }}"><i class="fa fa-angle-right"></i>{{ __('frontend.change_password') }}</a></li>
            </ul>
            
        </div>
        <!-- END SIDEBAR -->

        <!-- BEGIN CONTENT -->
        <div class="col-md-9 col-sm-7">
            <h1 class="text-right">{{ __('frontend.welcome') }} {{ auth('contact')->user()->name }}</h1>

            <div class="content-page">
                <div class="row text-center">
                    <div class="col-md-4 mb-3">
                        <div class="card shadow">
                            <div class="card-body">
                                <h5 class="card-title">{{ __('frontend.total_invoices') }}</h5>
                                <h2 class="counter text-primary">{{ $transactions->count() }}</h2>
                                <p class="text-muted">{{ __('frontend.total_invoices') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card shadow">
                            <div class="card-body">
                                <h5 class="card-title">{{ __('frontend.total_paid') }}</h5>
                                <h2 class="counter text-success">{{ $payments->sum('amount') }} {{ __('frontend.currency') }}</h2>
                                <p class="text-muted">{{ __('frontend.total_paid') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card shadow">
                            <div class="card-body">
                                <h5 class="card-title">{{ __('frontend.total_due') }}</h5>
                                <h2 class="counter text-danger">{{ auth('contact')->user()->balance }} {{ __('frontend.currency') }}</h2>
                                <p class="text-muted">{{ __('frontend.total_due') }}</p>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>

        </div>
        <!-- END CONTENT -->
    </div>
@endsection
