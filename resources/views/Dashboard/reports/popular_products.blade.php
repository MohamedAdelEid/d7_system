@extends('layouts.admin')

@section('title', trans('admin.popular_products') )


@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0"> {{ trans('admin.popular_products') }}</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('dashboard.home')}}">{{ trans('admin.Home') }}</a> / <a href="{{route('dashboard.users.index')}}">{{ trans('admin.Users') }}</a> / {{ trans('admin.Activity logs') }}</li>
            </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <!-- /.content-header -->
    <section class="content">
        <div class="container-fluid">
          <div class="card collapsed-card">
            <div class="card-header">
              <h3 class="card-title">{{ trans('admin.filter') }}</h3>
    
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>
                </button>
              </div>
              <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="col-lg-612">
                    <?php
                        $branchescollection = collect($branches)->pluck('name', 'id');
                        $categoryCollection = collect($categories)->pluck('name', 'id');

                    ?>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ trans('admin.branch') }}</label>
                                <select class="form-control select2 branch_id" name="branch_id" id="branch_id" style="width: 100%;">
                                    <option value="" selected >{{ trans('admin.Select') }}</option>
                                    @foreach ($branchescollection as $id => $name)
                                        <option value="{{ $id }}" @if (Request()->branch_id == $id) selected @endif>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                  
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ trans('admin.category') }}</label>
                                <select class="form-control select2" name="category_id" id="category_id" style="width: 100%;">
                                    <option value="" selected>{{ trans('admin.Select') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @if (Request()->category_id == $category->id) selected @endif>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    
           
                    </div>

                    
                </div>
            </div>
            <!-- /.card-body -->
          </div>
        </div>
      </section>
    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
            <!-- /.col -->
            <div class="col-md-12">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                        {{ trans('admin.popular_products') }}
                        </h3>
                    </div>


                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="stock-table"  class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>sku</th>
                            <th>{{ trans('admin.product_name') }}</th>
                            <th>{{ trans('admin.quantity') }}</th>
                            <th>{{ trans('admin.total') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                    
                        </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->

                    </div>
            </div>
            <!-- /.col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection


@section('script')
<script>
    $(document).ready(function() {
       var table = $('#stock-table').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                "url": "{{ route('dashboard.reports.popular.products') }}",
                "data": function ( d ) {
                d.category_id = $('#category_id').val();
                d.branch_id = $('#branch_id').val();
                } 
            } ,
            columns: [
                { data: 'sku', name: 'sku' },
                { data: 'name', name: 'name' },
                { data: 'quantity', name: 'quantity' },
                { data: 'total', name: 'total' }
            ]    , dom: 'lBfrtip'
            ,buttons: [
                    { extend: 'copy',  exportOptions: {search: 'none',columns: ':visible'}},
                    { extend: 'excel', exportOptions: {search: 'none',columns: ':visible'}},
                    { extend: 'csv',   exportOptions: {search: 'none',columns: ':visible'}},
                    { extend: 'pdf',   exportOptions: {search: 'none',columns: ':visible'}},
                    { extend: 'print', exportOptions: {search: 'none',columns: ':visible'}},
                    { extend: 'colvis', exportOptions: {search: 'none',columns: ':visible'}},
            ],
        });
        $(document).on('change', '#branch_id, #category_id' , function() {
            table.ajax.reload();
         });
    });
</script>
@endsection
