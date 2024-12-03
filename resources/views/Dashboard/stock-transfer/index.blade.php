@extends('layouts.admin')
@section('title', trans('admin.stock_transfers'))

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ trans('admin.stock_transfers') }}</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('dashboard.home')}}">{{ trans('admin.Home') }}</a> / {{ trans('admin.stock_transfers') }}</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                @if (auth('user')->user()->has_permission('create-stock-transfers'))
                    <a href="{{route('dashboard.stock-transfers.create')}}" type="button" class="btn btn-info">{{ trans('admin.Add') }}</a>
                @else
                    <a href="#" type="button" class="btn btn-info disabled">{{ trans('admin.Add') }}</a>
                @endif
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table class="table table-bordered table-striped data-table responsive">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ trans('admin.from_branch') }}</th>
                            <th>{{ trans('admin.to_branch') }}</th>
                            <th>{{ trans('admin.date') }}</th>
                            <th>{{ trans('admin.status') }}</th>
                            <th>{{ trans('admin.Actions') }}</th>
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
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            "url": "{{ route('dashboard.stock-transfers.index') }}",
            "data": function ( d ) {
                d.status = $('#status').val();
            }
        },
        createdRow: function(row, data, dataIndex) {
        // Make all cells in the row clickable except the last one (actions column)
            $(row).children('td:not(:last-child)').addClass('fire-popup')
                .attr('data-target', '#modal-default-big')
                .attr('data-toggle', 'modal')
                .attr('data-url', data.route)
                .css('cursor', 'pointer');
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'from_branch', name: 'from_branch'},
            {data: 'to_branch', name: 'to_branch'},
            {data: 'transaction_date', name: 'transaction_date'},
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
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

    $(document).on('change', '#status', function() {
        table.ajax.reload();
    });
</script>
@endsection