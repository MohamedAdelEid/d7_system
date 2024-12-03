@extends('layouts.admin')

@section('title', trans('admin.spoiled_products') )


@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0"> {{ trans('admin.spoiled_products') }}</h1>
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
                        $usersCollection = collect($users)->pluck('name', 'id');
                    ?>
                    <div class="row">
                        <div class="col-lg-4">
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
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>{{ trans('admin.category') }}</label>
                                <select class="form-control select2 category_id" name="category_id" id="category_id" style="width: 100%;">
                                    <option value="" selected >{{ trans('admin.Select') }}</option>
                                    @foreach ($categoryCollection as $id => $name)
                                        <option value="{{ $id }}" @if (Request()->category_id == $id) selected @endif>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>{{ trans('admin.Created by') }}</label>
                                <select class="form-control select2 user_id" name="created_by" id="created_by" style="width: 100%;">
                                    <option value="" selected >{{ trans('admin.Select') }}</option>
                                    @foreach ($usersCollection as $id => $name)
                                        <option value="{{ $id }}" @if (Request()->created_by == $id) selected @endif>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                     
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ trans('admin.date_from') }}</label>
                                <input type="date" class="form-control" name="date_from" id="date_from" value="{{ Request()->date_from }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ trans('admin.date_to') }}</label>
                                <input type="date" class="form-control" name="date_to" id="date_to" value="{{ Request()->date_to }}">
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
    <section class="content">
        <div class="container-fluid">
            <div class="row">
            <!-- /.col -->
            <div class="col-md-12">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                        {{ trans('admin.spoiled_products') }}
                        </h3>
                    </div>


                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="spoiled-table"  class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>sku</th>
                            <th>{{ trans('admin.product_name') }}</th>
                            <th>{{ trans('admin.branch') }}</th>
                            <th>{{ trans('admin.total') }}</th>
                            <th>{{ trans('admin.ref_no') }}</th>
                            <th>{{ trans('admin.date') }}</th>
                            <th>{{ trans('admin.Created by') }}</th>
                            <th>{{ trans('admin.reason') }}</th>
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
        var table =$('#spoiled-table').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                "url": "{{ route('dashboard.reports.spoiled.products') }}",
                "data": function ( d ) {
                d.category_id = $('#category_id').val();
                d.branch_id = $('#branch_id').val();
                d.created_by = $('#created_by').val();
                d.date_from = $('#date_from').val();
                d.date_to = $('#date_to').val();
                } 
            } ,
            columns: [
                { data: 'sku', name: 'sku' },
                { data: 'name', name: 'product_name' },
                { data: 'branch', name: 'branch' },
                { data: 'total', name: 'total' },
                { data: 'ref_no', name: 'ref_no' },
                { data: 'date', name: 'date' },
                { data: 'created_by', name: 'Created by' },
                { data: 'reason', name: 'reason' },
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
        $(document).on('change', '#branch_id, #category_id, #date_from, #date_to, #created_by', function() {
            table.ajax.reload();
    });
    });
  
</script>
@endsection
