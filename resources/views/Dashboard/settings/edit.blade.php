@extends('layouts.admin')

@section('title', trans('admin.Settings'))

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0">{{ trans('admin.Settings') }}</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('dashboard.home')}}">{{ trans('admin.Home') }}</a> / {{ trans('admin.Settings') }}</li>
            </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="container-fluid">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">
                <div class="card-header">
                <h3 class="card-title">{{ trans('admin.Edit') }}</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form method="post" action="">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                @include('components.form.input', [
                                    'class' => 'form-control',
                                    'name' => "site_name",
                                    'label' => trans('admin.Site name'),
                                    'value' => isset($data) ? $data->site_name : old('site_name'),
                                    'attribute' => 'required',
                                ])
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ trans('admin.Time zones') }}</label>
                                    <select class="form-control select2" style="width: 100%;" name="time_zone">
                                        @foreach (config('time_zones.blade') as $time_zone)
                                            <option value="{{$time_zone['name']}}" @if ($data->time_zone == $time_zone['name']) selected @endif>{{$time_zone['name']}}</option>
                                        @endforeach>
                                    </select>
                                    @error('time_zone')
                                        <span style="color: red; margin: 20px;">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ trans('admin.Date format') }}</label>
                                    <select class="form-control select2" style="width: 100%;" name="date_format">
                                        @foreach (config('date_formats.blade') as $date_format)
                                            <option value="{{$date_format['format']}}" @if ($data->date_format == $date_format['format']) selected @endif>{{date($date_format['format'])}}</option>
                                        @endforeach>
                                    </select>
                                    @error('date_formats')
                                        <span style="color: red; margin: 20px;">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="allow_unit_price_update" 
                                               name="allow_unit_price_update" 
                                               value="1"
                                               @if(isset($data) && $data->allow_unit_price_update) checked @endif>
                                        <label class="custom-control-label" for="allow_unit_price_update">
                                            {{ trans('admin.Allow users to update unit price when selling') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="prevent_buy_below_purchase_price" 
                                               name="prevent_buy_below_purchase_price" 
                                               value="1"
                                               @if(isset($data) && $data->prevent_buy_below_purchase_price) checked @endif>
                                        <label class="custom-control-label" for="prevent_buy_below_purchase_price">
                                            {{ trans('admin.Prevent selling below purchase price') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="display_total_in_invoice" 
                                           name="display_total_in_invoice" 
                                           value="1"
                                           @if(isset($data) && $data->display_total_in_invoice) checked @endif>
                                    <label class="custom-control-label" for="display_total_in_invoice">
                                        {{ trans('admin.Display total in invoice') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="display_discount_in_invoice" 
                                           name="display_discount_in_invoice" 
                                           value="1"
                                           @if(isset($data) && $data->display_discount_in_invoice) checked @endif>
                                    <label class="custom-control-label" for="display_discount_in_invoice">
                                        {{ trans('admin.Display discount in invoice') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="display_final_price_in_invoice" 
                                           name="display_final_price_in_invoice" 
                                           value="1"
                                           @if(isset($data) && $data->display_final_price_in_invoice) checked @endif>
                                    <label class="custom-control-label" for="display_final_price_in_invoice">
                                        {{ trans('admin.Display final price after discount in invoices') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="display_credit_details_in_invoice" 
                                           name="display_credit_details_in_invoice" 
                                           value="1"
                                           @if(isset($data) && $data->display_credit_details_in_invoice) checked @endif>
                                    <label class="custom-control-label" for="display_credit_details_in_invoice">
                                        {{ trans('admin.Display credit details in invoices') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="display_contact_info_in_invoice" 
                                           name="display_contact_info_in_invoice" 
                                           value="1"
                                           @if(isset($data) && $data->display_contact_info_in_invoice) checked @endif>
                                    <label class="custom-control-label" for="display_contact_info_in_invoice">
                                        {{ trans('admin.Display contact information in invoices') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="display_branch_info_in_invoice" 
                                           name="display_branch_info_in_invoice" 
                                           value="1"
                                           @if(isset($data) && $data->display_branch_info_in_invoice) checked @endif>
                                    <label class="custom-control-label" for="display_branch_info_in_invoice">
                                        {{ trans('admin.Display branch information in invoices') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="display_invoice_date_in_invoice" 
                                           name="display_invoice_date_in_invoice" 
                                           value="1"
                                           @if(isset($data) && $data->display_invoice_date_in_invoice) checked @endif>
                                    <label class="custom-control-label" for="display_invoice_date_in_invoice">
                                        {{ trans('admin.Display invoice date in invoice') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="display_created_by_in_invoice" 
                                           name="display_created_by_in_invoice" 
                                           value="1"
                                           @if(isset($data) && $data->display_created_by_in_invoice) checked @endif>
                                    <label class="custom-control-label" for="display_created_by_in_invoice">
                                        {{ trans('admin.Display created by information in invoices') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="display_ref_no_in_invoice" 
                                           name="display_ref_no_in_invoice" 
                                           value="1"
                                           @if(isset($data) && $data->display_ref_no_in_invoice) checked @endif>
                                    <label class="custom-control-label" for="display_ref_no_in_invoice">
                                        {{ trans('admin.Display ref no in invoice') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="classic_printing" 
                                           name="classic_printing" 
                                           value="1"
                                           @if(isset($data) && $data->classic_printing) checked @endif>
                                    <label class="custom-control-label" for="classic_printing">
                                        {{ trans('admin.Classic Printing') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="thermal_printing" 
                                           name="thermal_printing" 
                                           value="1"
                                           @if(isset($data) && $data->thermal_printing) checked @endif>
                                    <label class="custom-control-label" for="thermal_printing">
                                        {{ trans('admin.Thermal Printing') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">{{ trans('admin.Save') }}</button>
                    </div>
                </form>
            </div>
            <!-- /.card -->
            </div>
        </div><!-- /.container-fluid -->
    </section>
@endsection


