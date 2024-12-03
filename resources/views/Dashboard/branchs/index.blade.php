@extends('layouts.admin')

@section('title', trans('admin.branchs'))


@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0">{{ trans('admin.branchs') }}</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('dashboard.home')}}">{{ trans('admin.Home') }}</a> / {{ trans('admin.branchs') }}</li>
            </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!--start filter-->
    @include('Dashboard.branchs.filter')
    <!--end filter-->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header">
            @if (auth('user')->user()->has_permission('create-branchs'))
              <a href="{{route('dashboard.branchs.create')}}" type="button" class="btn btn-info">{{ trans('admin.Add') }}</a>
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
                <th>{{ trans('admin.Name') }}</th>
                <th>{{ trans('admin.cash_account') }}</th>
                <th>{{ trans('admin.credit_account') }}</th>
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
          "url": "{{ route('dashboard.branchs.index') }}",
          "data": function ( d ) {
            d.role = $('#role').val();
          }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'cash_account', name: 'cash_account'},
            {data: 'credit_account', name: 'credit_account'},
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
