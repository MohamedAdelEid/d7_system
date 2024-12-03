@extends('layouts.admin')

@section('title', trans('admin.importOpenStock'))

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('admin.importOpenStock') }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">{{ trans('admin.Home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.products.index') }}">{{ trans('admin.products') }}</a></li>
                        <li class="breadcrumb-item active">{{ trans('admin.importOpenStock') }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="container-fluid">
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
            <div class="row">
        
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{ trans('admin.importOpenStock') }}</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form action="{{ route('dashboard.products.importOpenStock') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <input type="file" name="excel-open-stock" class="form-control"><br>
                                <!-- Form elements here -->
                                <div class="form-group">
                                    <p>يجب ملء الجدول بالبيانات التالية:
                                        <ul>
                                            <li class="text-danger">الكود الخاص بالمنتج - {{ trans('admin.required*') }}</li>
                                            <li class="text-danger">اسم الفرع - {{ trans('admin.required*') }}</li>
                                            <li class="text-danger">الكمية المتاحة - {{ trans('admin.required*') }}</li>
                                        </ul>
                                    </p>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ trans('admin.import') }}</button>
                                <a href="{{ asset('files/products/template-open-stock.xlsx') }}" class="btn btn-success float-right" download>
                                    {{ trans('admin.download-open-stock-template') }}
                                </a>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                </div>
            </div><!-- /.container-fluid -->
    </section>
@endsection
