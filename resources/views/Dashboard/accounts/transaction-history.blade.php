@extends('layouts.admin')

@section('title', trans('admin.transaction_history'))

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('admin.transaction_history') }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">{{ trans('admin.Home') }}</a> /
                            {{ trans('admin.transaction_history') }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <!--start filter-->
    @include('Dashboard.accounts.filter_payment-history')
    <!--end filter-->
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ trans('admin.transaction_history_for') }} {{ $account->name }}</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table class="table table-bordered table-striped data-table">
                        <thead>
                            <tr>
                                <th>{{ trans('admin.number') }}</th>
                                <th>{{ trans('admin.date') }}</th>
                                <th>{{ trans('admin.contacts') }}</th>
                                <th>{{ trans('admin.type') }}</th>
                                <th>{{ trans('admin.method') }}</th>
                                <th>{{ trans('admin.operation') }}</th>
                                <th>{{ trans('admin.operation_type') }}</th>
                                <th>{{ trans('admin.account') }}</th>
                                <th>{{ trans('admin.Created by') }}</th>
                                <th>{{ trans('admin.amount') }}</th>
                                <th>{{ trans('admin.change_on_amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection

@section('script')
<script type="text/javascript">

    var table;

    $(function() {
        // Initialize the DataTable
        table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('dashboard.accounts.transaction-history', $account->id) }}",
                data: function (d) {
                
                    d.date_from = $('#date_from').val();
                    d.date_to = $('#date_to').val();
                }
            },
            columns: [
                { data: 'id', name: 'id', orderable: false },
                { data: 'created_at', name: 'created_at', orderable: false },
                { data: 'contact_name', name: 'contact_name', orderable: false },
                { data: 'contact_type', name: 'contact_type', orderable: false },
                { data: 'method', name: 'method', orderable: false },
                { data: 'operation', name: 'operation', orderable: false, render: function(data, type, row) {
                    if(data === 'subtract') {
                        return '<span class="badge badge-danger">خصم</span>';
                    } else if(data === 'add') {
                        return '<span class="badge badge-success">اضافة</span>';
                    }
                    return data;
                } },
                { data: 'type', name: 'type', orderable: false , render: function(data, type, row) {
                    if(data === 'expense') {
                        return '<span class="badge badge-danger">مصروف</span>';
                    } else if(data === 'sell') {
                        return '<span class="badge badge-success">مبيعات</span>';
                    }
                    else if(data === 'purchase') {
                        return '<span class="badge badge-info">مشتريات</span>';
                    }else{
                        return '<span class="badge badge-secondary">مدفوعات</span>';
                    }
                    return data;
                } },
                { data: 'account_name', name: 'account_name', orderable: false },
                { data: 'created_by', name: 'created_by', orderable: false },
                { data: 'amount', name: 'amount', orderable: false },
                { data: 'change_amount', name: 'change_amount', orderable: false }
            ],
            dom: 'lBfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });

   
    $(document).on('change', '#date_from, #date_to', function() {
        table.ajax.reload();
    });
</script>
@endsection
