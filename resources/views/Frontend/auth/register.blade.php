@extends('layouts.frontend')

@section('title', trans('frontend.create_account'))

@section('content')
    <ul class="breadcrumb">
        <li><a href="index.html">{{ trans('frontend.home') }}</a></li>
        <li class="active">{{ trans('frontend.create_account') }}</li>
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

                            <div class="col-md-12 col-sm-12">
                                <h3>{{ trans('frontend.create_account') }}</h3>
                                <p>{{ trans('frontend.already_have_account') }} <a href="{{ route('login') }}">{{ trans('frontend.login') }}</a></p>
                                @if ($errors->has('login_error'))
                                    <div class="alert alert-danger p-1">
                                        {{ $errors->first('login_error') }}
                                    </div>
                                @endif

                                <form role="form" action="{{ route('register') }}" method="POST">
                                    @csrf
                                    <div class="panel-body row">
                                        <div class="form-group col-md-6 col-sm-6">
                                            <label for="name">{{ trans('frontend.name') }} <span class="require">*</span></label>
                                            <input type="text" id="name" name="name" class="form-control" required>
                                        </div>
                                        <div class="form-group col-md-6 col-sm-6">
                                            <label for="activity_type_id">{{ trans('frontend.activity_type') }} <span class="require">*</span></label>
                                            <select class="form-control" id="activity_type_id" name="activity_type_id" required>
                                                <option value="">{{ trans('frontend.select') }}</option>
                                                @foreach ($activityTypes as $activityType)
                                                    <option value="{{ $activityType->id }}">{{ $activityType->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6 col-sm-6">
                                            <label for="phone">{{ trans('frontend.phone') }} <span class="require">*</span></label>
                                            <input type="text" id="phone" name="phone" class="form-control" required>
                                        </div>
                                        <div class="form-group col-md-6 col-sm-6">
                                            <label for="email">{{ trans('frontend.email') }} <span class="require">*</span></label>
                                            <input type="email" id="email" name="email" class="form-control" required>
                                        </div>

                                        <div class="form-group col-md-6 col-sm-6">
                                            <label for="password">{{ trans('frontend.password') }} <span class="require">*</span></label>
                                            <input type="password" id="password" name="password" class="form-control" required>
                                        </div>

                                        <div class="form-group col-md-6 col-sm-6">
                                            <label for="password_confirmation">{{ trans('frontend.password_confirmation') }} <span class="require">*</span></label>
                                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                                        </div>

                                        <div class="form-group col-md-12 col-sm-12">
                                            <label for="address">{{ trans('frontend.address') }} <span class="require">*</span></label>
                                            <input type="text" id="address" name="address" class="form-control" required>
                                        </div>

                                        <div class="form-group col-md-6 col-sm-6">
                                            <label for="city_id">{{ trans('frontend.city') }} <span class="require">*</span></label>
                                            <select class="form-control" id="city_id" name="city_id" required>
                                                <option value="">{{ trans('frontend.select') }}</option>
                                                @foreach ($cities as $city)
                                                    <option value="{{ $city->id }}">
                                                        {{ app()->getLocale() == 'ar' ? $city->city_name_ar : $city->city_name_en }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 col-sm-6">
                                            <label for="governorate_id">{{ trans('frontend.governorate') }} <span class="require">*</span></label>
                                            <select class="form-control" id="governorate_id" name="governorate_id" required>
                                                <option value="">{{ trans('frontend.select') }}</option>
                                                @foreach ($governorates as $governorate)
                                                    <option value="{{ $governorate->id }}">
                                                        {{ app()->getLocale() == 'ar' ? $governorate->governorate_name_ar : $governorate->governorate_name_en }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-sm-6 padding-top-20">
                                            <button class="btn btn-primary" type="submit">{{ trans('frontend.create_account') }}</button>
                                        </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END CHECKOUT -->
            </div>
            <!-- END CHECKOUT PAGE -->
        </div>
        <!-- END CONTENT -->
    </div>
@endsection
