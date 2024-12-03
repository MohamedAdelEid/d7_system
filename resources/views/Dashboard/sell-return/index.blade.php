@extends('layouts.admin')

@section('title', trans('admin.sell-return'))


@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0">{{ trans('admin.sell-return') }}</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('dashboard.home')}}">{{ trans('admin.Home') }}</a> / {{ trans('admin.sell-return') }}</li>
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
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <table class="table table-bordered table-striped data-table responsive">
              <thead>
              <tr>
                <th>#</th>
                <th>{{ trans('admin.total') }}</th>
                <th>{{ trans('admin.contact') }}</th>
                <th>{{ trans('admin.phone') }}</th>
                <th>{{ trans('admin.government') }}</th>
                <th>{{ trans('admin.city') }}</th>
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
          "url": "{{ route('dashboard.sells.sell-return.index') }}",
          "data": function ( d ) {
            d.role = $('#role').val();
          }
        },
        columnDefs: [{
                    targets: 1,
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).addClass('fire-popup')
                            .attr('data-target', '#modal-default-big')
                            .attr('data-toggle', 'modal')
                            .attr('data-url', rowData.route)
                            .attr('style', 'cursor: pointer');
                    }
                },
                {
                    targets: 2,
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).addClass('fire-popup')
                            .attr('data-target', '#modal-default-big')
                            .attr('data-toggle', 'modal')
                            .attr('data-url', rowData.route)
                            .attr('style', 'cursor: pointer');
                    }
                },
                {
                    targets: 3,
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).addClass('fire-popup')
                            .attr('data-target', '#modal-default-big')
                            .attr('data-toggle', 'modal')
                            .attr('data-url', rowData.route)
                            .attr('style', 'cursor: pointer');
                    }
                },
                {
                    targets: 4,
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).addClass('fire-popup')
                            .attr('data-target', '#modal-default-big')
                            .attr('data-toggle', 'modal')
                            .attr('data-url', rowData.route)
                            .attr('style', 'cursor: pointer');
                    }
                },
                {
                    targets: 5,
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).addClass('fire-popup')
                            .attr('data-target', '#modal-default-big')
                            .attr('data-toggle', 'modal')
                            .attr('data-url', rowData.route)
                            .attr('style', 'cursor: pointer');
                    }
                },
                {
                    targets: 6,
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).addClass('fire-popup')
                            .attr('data-target', '#modal-default-big')
                            .attr('data-toggle', 'modal')
                            .attr('data-url', rowData.route)
                            .attr('style', 'cursor: pointer');
                    }
                },
            ],
        columns: [
            {data: 'id', name: 'id'},
            {data: 'total', name: 'total'},
            {data: 'contact', name: 'contact'},
            {data: 'phone', name: 'phone'},
            {data: 'government', name: 'government'},
            {data: 'city', name: 'city'},
            {data: 'created_at', name: 'created_at'},
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

  $(document).on('change', '#role', function() {
    table.ajax.reload();
  });
</script>
@endsection
