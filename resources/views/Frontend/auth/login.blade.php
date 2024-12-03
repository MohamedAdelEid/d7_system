@extends('layouts.frontend')

@section('title', trans('frontend.login'))

@section('content')
    <ul class="breadcrumb">
        <li><a href="{{ route('index') }}">{{ trans('frontend.home') }}</a></li>
        <li class="active">{{ trans('frontend.login') }}</li>
    </ul>
    <div class="row margin-bottom-40">
        <!-- BEGIN CONTENT -->
        <div class="col-md-12 col-sm-12">

            <!-- BEGIN CHECKOUT PAGE -->
            <div class="panel-group checkout-page accordion scrollable" id="checkout-page">

                <!-- BEGIN CHECKOUT -->
                <div id="checkout" class="panel panel-default">

                    <div id="checkout-content" class="panel-collapse collapse in">
                        <div class="panel-body row">
                           
                            <div class="col-md-6 col-sm-6">
                                <h3>{{ trans('frontend.login') }}</h3>
                                <p>{{ trans('frontend.no_account') }} <a href="{{ route('register') }}">{{ trans('frontend.create_account') }}</a></p>
                                    @if ($errors->has('login_error'))
                                        <div class="alert alert-danger p-1">
                                            {{ $errors->first('login_error') }}
                                        </div>
                                    @endif
                                
                                <form role="form" action="{{ route('login') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="email-login">{{ trans('frontend.email') }}</label>
                                        <input type="text" name="email" id="email-login" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="password-login">{{ trans('frontend.password') }}</label>
                                        <input type="password" id="password-login" name="password" class="form-control">
                                    </div>
                                    {{-- <a href="javascript:;">Forgotten Password?</a> --}}
                                    <div class="padding-top-20">
                                        <button class="btn btn-primary" type="submit"> {{ trans('frontend.login') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END CHECKOUT -->


                <!-- END CONFIRM -->
            </div>
            <!-- END CHECKOUT PAGE -->
        </div>
        <!-- END CONTENT -->
    </div>
@endsection
