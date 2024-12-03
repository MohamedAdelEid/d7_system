@extends('layouts.admin')

@section('title', trans('admin.EditProductsSettings'))

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
                <h3 class="card-title">{{ trans('admin.EditProductsSettings') }}</h3>
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
                                               id="display_brands" 
                                               name="display_brands" 
                                               value="1"
                                               @if(isset($data) && $data->display_brands) checked @endif>
                                        <label class="custom-control-label" for="display_brands">
                                            {{ trans('admin.Active Brands') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="display_main_categories" 
                                               name="display_main_categories" 
                                               value="1"
                                               @if(isset($data) && $data->display_main_category) checked @endif>
                                        <label class="custom-control-label" for="display_main_categories">
                                            {{ trans('admin.Active Main Categories') }}
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
                                               id="display_sub_categories" 
                                               name="display_sub_category" 
                                               value="1"
                                               @if(isset($data) && $data->display_sub_category) checked @endif>
                                        <label class="custom-control-label" for="display_sub_categories">
                                            {{ trans('admin.Active Sub Categories') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="display_sub_units" 
                                               name="display_sub_units" 
                                               value="1"
                                               @if(isset($data) && $data->display_sub_units) checked @endif>
                                            <label class="custom-control-label" for="display_sub_units">
                                            {{ trans('admin.Active Sub Units') }}
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
        </div>
        <!-- /.container-fluid -->
    </section>
@endsection


