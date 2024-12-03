@extends('layouts.frontend')

@section('title', trans('frontend.login'))

@section('content')
    <ul class="breadcrumb">
        <li><a href="{{ route('index') }}">{{ trans('frontend.home') }}</a></li>
        <li class="active">{{ trans('frontend.profile') }} </li>
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
            <h1 class="text-right">تعديل البيانات</h1>
            <div class="content-page">
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
                <div class="row ">
                    <form action="{{ route('profile.edit') }}" method="post">
                        @csrf
                        <div class="panel-body row">
                            <div class="form-group col-md-6 col-sm-6">
                                <label for="name">{{ trans('frontend.name') }} <span class="require">*</span></label>
                                <input type="text" id="name" name="name" class="form-control" value="{{ $contact->name }}" required>
                            </div>
                            <div class="form-group col-md-6 col-sm-6">
                                <label for="activity_type_id">{{ trans('frontend.activity_type') }} <span class="require">*</span></label>
                                <select class="form-control" id="activity_type_id" name="activity_type_id" required>
                                    <option value="">{{ trans('frontend.select') }}</option>
                                    @foreach ($activityTypes as $activityType)
                                        <option value="{{ $activityType->id }}" {{ $contact->activity_type_id == $activityType->id ? 'selected' : ''  }}>{{ $activityType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6 col-sm-6">
                                <label for="phone">{{ trans('frontend.phone') }} <span class="require">*</span></label>
                                <input type="text" id="phone" name="phone" class="form-control" value="{{ $contact->phone }}" required>
                            </div>
                            <div class="form-group col-md-6 col-sm-6">
                                <label for="email">{{ trans('frontend.email') }} <span class="require">*</span></label>
                                <input type="email" id="email" name="email" class="form-control" value="{{ $contact->email }}" required>
                            </div>


                            <div class="form-group col-md-12 col-sm-12">
                                <label for="address">{{ trans('frontend.address') }} <span class="require">*</span></label>
                                <input type="text" id="address" name="address" class="form-control" value="{{ $contact->address }}" required>
                            </div>

                            <div class="form-group col-md-6 col-sm-6">
                                <label for="city_id">{{ trans('frontend.city') }} <span class="require">*</span></label>
                                <select class="form-control"  id="city_id" name="city_id" required>
                                    <option value="">{{ trans('frontend.select') }}</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}" {{ $contact->city_id == $city->id ? 'selected' : ''  }}>
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
                                        <option value="{{$governorate->id }}" {{ $contact->governorate_id == $governorate->id ? 'selected' : ''  }} >
                                            {{ app()->getLocale() == 'ar' ? $governorate->governorate_name_ar : $governorate->governorate_name_en }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 col-sm-6 padding-top-20">
                                <button class="btn btn-success" type="submit">{{ __('frontend.save') }}</button>
                            </div>
                    </form>
                </div>
            </div>

        </div>
        <!-- END CONTENT -->
    </div>
@endsection
