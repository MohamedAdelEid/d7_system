@extends('layouts.admin')

@section('title', trans('admin.Activity Logs'))


@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0">{{ trans('admin.Activity Logs') }}</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('dashboard.home')}}">{{ trans('admin.Home') }}</a> / {{ trans('admin.Activity Logs') }}</li>
            </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    @include('Dashboard.activity-log.filter')
    <!-- Main content -->
    <section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ trans('admin.Activity Logs') }}</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="activityLogTable" class="table table-bordered table-striped data-table">
                        <thead>
                            <tr>
                                <th>{{ trans('admin.Index') }}</th>
                                <th>{{ trans('admin.Title') }}</th>
                                <th>{{ trans('admin.User') }}</th>
                                <th>{{ trans('admin.Description') }}</th>
                                <th>{{ trans('admin.Proccess Type') }}</th>
                                <th>{{ trans('admin.Created at') }}</th>
                                <th>{{ trans('admin.Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
    </section>
    <!-- /.content -->
@endsection

@section('script')
    <script type="text/javascript">
        var table = $('#activityLogTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{ route('dashboard.activity-log.index') }}",
                "data": function(d) {
                    d.user_id = $('#user_id').val();
                    d.date_from = $('#date_from').val();
                    d.date_to = $('#date_to').val();
                    d.proccess_type = $('#proccess_type').val();
                }
            },
           
            order: [[0, 'desc']], // Order by the first column (id) in descending order
            columns: [
                {data: 'id', name: 'id'},
                {data: 'title', name: 'title'},
                {data: 'user', name: 'user'},
                {data: 'description', name: 'description'},
                {data: 'proccess_type', name: 'proccess_type', render: function(data, type, row, meta){
                   switch(data){
                       case 'create':
                           return '<span class="badge bg-success">إضافة</span>';
                       case 'update':
                           return '<span class="badge bg-info">تعديل</span>';
                       case 'delete':
                           return '<span class="badge bg-danger">حذف</span>';
                       case 'accounts':
                           return '<span class="badge bg-warning">حسابات</span>';
                       case 'suppliers':
                           return '<span class="badge bg-primary">موردين</span>';
                       case 'customers':
                           return '<span class="badge bg-secondary">عملاء</span>';
                       case 'products':
                           return '<span class="badge bg-blue">منتجات</span>';
                       case 'sales':
                           return '<span class="badge bg-dark">مبيعات</span>';
                       case 'purchase':
                           return '<span class="badge bg-dark">مشتريات</span>';
                       case 'stock_transfer':
                           return '<span class="badge bg-dark">تحويل مخزون</span>';
                       case 'expenses':
                           return '<span class="badge bg-dark">مصروفات</span>';
                       case 'spoiled_stock':
                           return '<span class="badge bg-dark">مخزون تالف</span>';
                       default:
                           return data;
                   }
                }},
                {data: 'created_at', name: 'created_at'},
                {data: 'action', name: 'action'},
            ],
            dom: 'lBfrtip',
            buttons: [
                { extend: 'copy',  exportOptions: {search: 'none',columns: ':visible'}},
                { extend: 'excel', exportOptions: {search: 'none',columns: ':visible'}},
                { extend: 'csv',   exportOptions: {search: 'none',columns: ':visible'}},
                { extend: 'pdf',   exportOptions: {search: 'none',columns: ':visible'}},
                { extend: 'print', exportOptions: {search: 'none',columns: ':visible'}},
                { extend: 'colvis', exportOptions: {search: 'none',columns: ':visible'}},
            ],
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']]
        });
        $(document).on('change', '#user_id, #date_from, #date_to, #proccess_type', function() {
            table.ajax.reload();
        });
    </script>
@endsection
