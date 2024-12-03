@extends('layouts.admin')

@section('title', trans('admin.sales_segments'))


@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0">{{ trans('admin.sales_segments') }}</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('dashboard.home')}}">{{ trans('admin.Home') }}</a> / {{ trans('admin.sales_segments') }}</li>
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
            @if (auth('user')->user()->has_permission('create-sales-segments'))
              <a href="{{route('dashboard.sales-segments.create')}}" type="button" class="btn btn-info">{{ trans('admin.add_sales_segment') }}</a>
            @else
              <a href="#" type="button" class="btn btn-info disabled">{{ trans('admin.add_sales_segment') }}</a>
            @endif
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <table class="table table-bordered table-striped data-table responsive">
              <thead>
              <tr>
                <th>#</th>
                <th>{{ trans('admin.Name') }}</th>
                <th>{{ trans('admin.Description') }}</th>
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
          "url": "{{ route('dashboard.sales-segments.index') }}",
          "data": function ( d ) {
            d.role = $('#role').val();
          }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'description', name: 'description'},
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
