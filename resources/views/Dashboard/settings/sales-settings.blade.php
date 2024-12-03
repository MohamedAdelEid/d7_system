@extends('layouts.admin')

@section('title', trans('admin.EditSalesSettings'))  

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
                <h3 class="card-title">{{ trans('admin.EditSalesSettings') }}</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form method="post" action="">
                    @csrf
                    <div class="card-body">
                     
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


