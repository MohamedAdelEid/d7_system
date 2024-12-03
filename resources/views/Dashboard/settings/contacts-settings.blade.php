@extends('layouts.admin')

@section('title', trans('admin.EditContactsSettings'))

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
                <h3 class="card-title">{{ trans('admin.EditContactsSettings') }}</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form method="post" action="" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                @include('components.form.input', [
                                    'class' => 'form-control',
                                    'name' => "default_credit_limit",
                                    'label' => trans('admin.Default Credit Limit'),
                                    'value' => isset($data) ? $data->default_credit_limit : old('default_credit_limit'),
                                    'attribute' => 'required',
                                ])
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


