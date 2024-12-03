@extends('layouts.frontend')

@section('title', trans('frontend.profile'))

@section('content')
    <ul class="breadcrumb">
        <li><a href="{{ route('index') }}">{{ trans('frontend.home') }}</a></li>
        <li class="active">{{ trans('frontend.profile') }}</li>
    </ul>
    <div class="row margin-bottom-40">
        <!-- BEGIN SIDEBAR -->
        <div class="sidebar col-md-3 col-sm-3">
            <ul class="list-group margin-bottom-25 sidebar-menu">
                <li class="list-group-item clearfix  {{ request()->routeIs('profile') ? 'active' : '' }}"><a
                        href="{{ route('profile') }}"><i class="fa fa-angle-right"></i>{{ __('frontend.profile') }}</a></li>
                <li class="list-group-item clearfix  {{ request()->routeIs('profile.transaction') ? 'active' : '' }}"><a
                        href="{{ route('profile.transaction') }}"><i
                            class="fa fa-angle-right"></i>{{ __('frontend.my_orders') }}</a></li>
                <li class="list-group-item clearfix {{ request()->routeIs('profile.edit') ? 'active' : '' }}"><a
                        href="{{ route('profile.edit') }}"><i
                            class="fa fa-angle-right"></i>{{ __('frontend.edit_profile') }}</a></li>
                <li class="list-group-item clearfix {{ request()->routeIs('profile.password') ? 'active' : '' }}"><a
                        href="{{ route('profile.password') }}"><i
                            class="fa fa-angle-right"></i>{{ __('frontend.change_password') }}</a></li>
            </ul>

        </div>
        <!-- END SIDEBAR -->

        <!-- BEGIN CONTENT -->
        <div class="col-md-9 col-sm-7">
            <h1 class="text-right">{{ __('frontend.change_password') }}</h1>
            @if (session('error'))
                <div class="alert alert-danger p-1">
                    {{ session('error') }}
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success p-1">
                    {{ session('success') }}
                </div>
            @endif
            <div class="content-page">
                <div class="row ">
                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        <div class="panel-body row">
                            <div class="form-group col-md-12 col-sm-12">
                                <label for="current_password">{{ __('frontend.current_password') }}<span
                                        class="require">*</span></label>
                                <input type="password" id="current_password" name="current_password" class="form-control"
                                    required>
                            </div>

                            <div class="form-group col-md-12 col-sm-12">
                                <label for="new_password">{{ __('frontend.new_password') }}<span
                                        class="require">*</span></label>
                                <input type="password" id="new_password" name="new_password" class="form-control" required>
                            </div>

                            <div class="form-group col-md-12 col-sm-12">
                                <label for="new_password_confirmation">{{ __('frontend.new_password_confirmation') }}<span
                                        class="require">*</span></label>
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                                    class="form-control" required>
                            </div>

                            <div class="col-md-6 col-sm-6 padding-top-20">
                                <button class="btn btn-success" type="submit">{{ __('frontend.save') }}</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

        </div>
        <!-- END CONTENT -->
    </div>
@endsection
