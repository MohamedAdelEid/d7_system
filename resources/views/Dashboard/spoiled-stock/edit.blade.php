@extends('layouts.admin')

@section('title', trans('admin.spoiled_stock'))

@section('content')
<style>
    .modal-dialog{
        max-width: 1000px;
    }
</style>
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0">{{ trans('admin.spoiled_stock') }}</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('dashboard.home')}}">{{ trans('admin.Home') }}</a> / <a href="{{route('dashboard.spoiled-stock.index')}}">{{ trans('admin.spoiled_stock') }}</a> / {{ trans('admin.Edit') }}</li>
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
                <form method="post" action="{{route('dashboard.spoiled-stock.update', $spoiledStock->id)}}" id="spoiledStockForm">
                    @csrf
                
                    @include('Dashboard.spoiled-stock.edit-form')
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">{{ trans('admin.update') }}</button>
                    </div>
                </form>
            </div>
            <!-- /.card -->
            </div>
        </div><!-- /.container-fluid -->
    </section>
@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        $('#spoiledStockForm').on('change', '.branch_id', function() {
            var branch_id = $(this).val();
            $.ajax({
                url: "{{route('dashboard.products.ProudctsByBranch')}}",
                method: 'GET',
                data: { branch_id: branch_id },
                success: function(data) {
                    $(".products").html(data);
                },
                error: function() {
                    alert('Error occurred while fetching products');
                }
            });
        });

        $('#spoiledStockForm').on('change', '.product_spoiled_add', function() {
            var product_id = $(this).val();
            var branch_id = $(".branch_id").val();
            $.ajax({
                url: "{{route('dashboard.spoiled-stock.ProductRowAdd')}}",
                method: 'GET',
                data: { 
                    product_id: product_id,
                    branch_id: branch_id
                },
                success: function(data) {
                    $(".spoiled_stock_table").append(data);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                    console.log("Response Text:", xhr.responseText);
                    alert('Error occurred while adding product row. Please check the console for more details.');
                }
            });
        });

        $('#spoiledStockForm').on('click', '.remove_row', function() {
            var product_row_id = $(this).data('product_row_id');
            $('.product_row_' + product_row_id).remove();
        });

        $('#spoiledStockForm').on('change', '.product_row_quantity', function() {
            var product_row_id = $(this).data('product_row_id');
            var quantity = $(this).val();
            var available_quantity = $('.product_row_available_quantity_' + product_row_id).val();
            
            if (parseInt(quantity) > parseInt(available_quantity)) {
                alert('Quantity cannot exceed available quantity');
                $(this).val(available_quantity);
            }
        });

        // Add event listener for remove-line button
        $('.spoiled_stock_table').on('click', '.remove-line', function() {
            $(this).closest('tr').remove();
        });
    });
</script>
@endsection
