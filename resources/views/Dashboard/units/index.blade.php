@extends('layouts.admin')

@section('title', trans('admin.units'))


@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0">{{ trans('admin.units') }}</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('dashboard.home')}}">{{ trans('admin.Home') }}</a> / {{ trans('admin.units') }}</li>
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
            @if (auth('user')->user()->has_permission('create-units'))
              <a href="{{route('dashboard.units.create')}}" type="button" class="btn btn-info">{{ trans('admin.Add') }}</a>
            @else
              <a href="#" type="button" class="btn btn-info disabled">{{ trans('admin.Add') }}</a>
            @endif
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <table class="table table-bordered table-striped data-table">
              <thead>
              <tr>
                <th>#</th>
                <th>{{ trans('admin.actual_name') }}</th>
                <th>{{ trans('admin.short_name') }}</th>
                <th>{{ trans('admin.base_unit_multiplier') }}</th>
                <th>{{ trans('admin.base_unit') }}</th>
                <th>{{ trans('admin.Created at') }}</th>
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
          "url": "{{ route('dashboard.units.index') }}",
          "data": function ( d ) {
            // d.role = $('#role').val();
          }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'actual_name', name: 'actual_name'},
            {data: 'short_name', name: 'short_name'},
            {data: 'base_unit_multiplier', name: 'base_unit_multiplier'},
            {data: 'base_unit', name: 'base_unit'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        dom: 'lBfrtip',
        buttons: [
                    { extend: 'copy',  exportOptions: { modifier: { page: 'all', search: 'none' } } },
                    { extend: 'excel', exportOptions: { modifier: { page: 'all', search: 'none' } } },
                    { extend: 'csv',   exportOptions: { modifier: { page: 'all', search: 'none' } } },
                    { extend: 'pdf',   exportOptions: { modifier: { page: 'all', search: 'none' } } },
                    { extend: 'print', exportOptions: { modifier: { page: 'all', search: 'none' } } },
                    { extend: 'colvis', exportOptions: { modifier: { page: 'all', search: 'none' } } },
                ],
        "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, 'All'] ]
    });

  $(document).on('change', '#role', function() {
    table.ajax.reload();
  });
</script>
@endsection
