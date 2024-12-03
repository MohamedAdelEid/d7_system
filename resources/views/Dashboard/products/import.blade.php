@extends('layouts.admin')

@section('title', trans('admin.import-products'))

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('admin.import-products') }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">{{ trans('admin.Home') }}</a> / <a
                                href="{{ route('dashboard.products.index') }}">{{ trans('admin.products') }}</a> /
                            {{ trans('admin.Create') }}</li>
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
                            <h3 class="card-title">{{ trans('admin.Create') }}</h3>
                     
                            
                        </div>
                        <!-- /.card-header -->
                        <div class="alert alert-info mt-2">
                            <h5>{{ trans('admin.uploadInstructions') }}</h5>
                            <p>{{ trans('admin.instructionsDescription') }}</p>
                            <ul>
                                <li> (name) : {{ trans('admin.productNameInstruction') }} - ({{ trans('admin.required*') }})</li>
                                <li> (sku) : {{ trans('admin.skuInstruction') }} - ({{ trans('admin.optional') }})</li>
                                <li> (description) : {{ trans('admin.descriptionInstruction')  }} - ({{ trans('admin.optional') }})</li>
                                <li> (main_unit_name) : {{ trans('admin.mainUnitInstruction') }} - ({{ trans('admin.required*') }})</li>
                                <li> (main_unit_sale_price) : {{ trans('admin.main_unit_sale_price') }} - ({{ trans('admin.required*') }})</li>
                                <li> (main_unit_purchase_price) : {{ trans('admin.main_unit_purchase_price') }} - ({{ trans('admin.required*') }})</li>
                                <li> (sub_unit_name) : {{ trans('admin.subUnitInstruction') }} - ({{ trans('admin.optional') }})</li>
                                <li> (sub_unit_sale_price) : {{ trans('admin.sub_unit_sale_price') }} - ({{ trans('admin.optional') }})</li>
                                <li> (sub_unit_purchase_price) : {{ trans('admin.sub_unit_purchase_price') }} - ({{ trans('admin.optional') }})</li>
                                <li> (brand_name) : {{ trans('admin.brandNameInstruction')  }} - ({{ trans('admin.optional') }})</li>
                                <li> (main_category_name) : {{ trans('admin.mainCategoryInstruction') }} - ({{ trans('admin.required*') }})</li>
                                <li> (sub_category_name) : {{ trans('admin.categoryInstruction') }} - ({{ trans('admin.optional') }})</li>
                                <li> (min_sale) : {{ trans('admin.minSaleInstruction') }} - ({{ trans('admin.required*') }})</li>
                                <li> (max_sale) : {{ trans('admin.maxSaleInstruction') }} - ({{ trans('admin.required*') }})</li>
                                <li> (quantity_alert) : {{ trans('admin.quantityAlertInstruction') }} - ({{ trans('admin.required*') }})</li>
                                <li> (for_sale) : {{ trans('admin.forSaleInstruction') }} - ({{ trans('admin.optional') }})</li>
                                <li> (branch) : {{ trans('admin.branchIdsInstruction')  }} - ({{ trans('admin.required') }})</li>
                                <li> (sales_segment_prices) : {{ trans('admin.segmentPricesInstruction') }} - ({{ trans('admin.optional') }})</li>
                                <li> (iamge_name) : {{ trans('admin.imageNameInstruction') }} - ({{ trans('admin.optional') }})</li>
                                <!-- Add any other specific instructions for other fields as needed -->
                            </ul>
                        </div>
                        <!-- form start -->
                        <form method="post" action="{{ route('dashboard.products.import') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <x-form.file class="form-control" name="excel" attribute=""
                                            label="{{ trans('admin.excel') }}" />
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ trans('admin.Add') }}</button>

                                <a href="{{ asset('files/products/sample_products_import.xlsx') }}" class="btn btn-success float-right" download>
                                    {{ trans('admin.downloadProductTemplate') }}
                                </a>
                            </div>
                        </form>
                        
                    </div>
                    <!-- /.card -->
                </div>
            </div><!-- /.container-fluid -->
    </section>
@endsection
