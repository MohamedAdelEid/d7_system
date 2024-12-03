@extends('layouts.admin')

@section('title', trans('admin.Expenses') )


@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0"> {{ trans('admin.Expenses') }}</h1>
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
@include('Dashboard.reports.filter')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
            <!-- /.col -->
            <div class="col-md-12">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                        {{ trans('admin.Expenses') }}
                        </h3>
                    </div>


                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="expenses-table"  class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>{{ trans('admin.category') }}</th>
                            <th>{{ trans('admin.branch') }}</th>
                            <th>{{ trans('admin.amount') }}</th>
                            <th>{{ trans('admin.date') }}</th>
                            <th>{{ trans('admin.Created by') }}</th>
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
   var table = $('#expenses-table').DataTable({
        processing: true,
        serverSide: true,
            ajax:{
                "url": "{{ route('dashboard.reports.expenses') }}",
                "data": function ( d ) {
                d.category_id = $('#category_id').val();
                d.branch_id = $('#branch_id').val();
                d.date_from = $('#date_from').val();
                d.date_to = $('#date_to').val();
                d.created_by = $('#created_by').val();
                } 
            } ,
            columns: [
                { data: 'amount', name: 'amount' },
                { data: 'category', name: 'category' },
                { data: 'branch', name: 'branch' },
                { data: 'date', name: 'date' },
                { data: 'created_by', name: 'created_by' }
            ]
            , dom: 'lBfrtip'
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
</script>
@endsection
