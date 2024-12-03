@extends('layouts.admin')

@section('title', trans('admin.EditInvoiceSettings'))

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
                <h3 class="card-title">{{ trans('admin.EditInvoiceSettings') }}</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form method="post" action="" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                    
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
                                <div class="custom-control custom-radio">
                                    <input type="radio" 
                                           class="custom-control-input printing-option" 
                                           id="classic_printing" 
                                           name="printing_option" 
                                           value="classic"
                                           @if(isset($data) && $data->classic_printing) checked @endif>
                                    <label class="custom-control-label" for="classic_printing">
                                        {{ trans('admin.Classic Printing') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="custom-control custom-radio">
                                    <input type="radio" 
                                           class="custom-control-input printing-option" 
                                           id="thermal_printing" 
                                           name="printing_option" 
                                           value="thermal"
                                           @if(isset($data) && $data->thermal_printing) checked @endif>
                                    <label class="custom-control-label" for="thermal_printing">
                                        {{ trans('admin.Thermal Printing') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ trans('admin.image_invoice') }}</label>
                                <input type="file" class="form-control" name="image_invoice" accept="image/*">
                                @error('image_invoice')
                                    <span style="color: red; margin: 20px;">
                                        {{ $message }}
                                    </span>
                                @enderror
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
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.printing-option');
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    // Uncheck all other checkboxes
                    checkboxes.forEach(cb => {
                        if (cb !== this) {
                            cb.checked = false;
                        }
                    });
                }
            });
        });
    });
    </script>
@endpush

