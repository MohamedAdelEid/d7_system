@extends('layouts.admin')

@section('title', trans('admin.accounts'))


@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0">{{ trans('admin.accounts') }}</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('dashboard.home')}}">{{ trans('admin.Home') }}</a> / {{ trans('admin.accounts') }}</li>
            </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!--start filter-->
    @include('Dashboard.accounts.filter')
    <!--end filter-->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header">
            @if (auth('user')->user()->has_permission('create-accounts'))
              <a href="{{route('dashboard.accounts.create')}}" type="button" class="btn btn-info">{{ trans('admin.Add') }}</a>
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
                    <th>{{ trans('admin.name') }}</th>
                    <th>{{ trans('admin.number') }}</th>
                    <th>{{ trans('admin.balance') }}</th>
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
          "url": "{{ route('dashboard.accounts.index') }}",
          "data": function ( d ) {
            d.type = $('.type').val();
          }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'number', name: 'number'},
            {data: 'balance', name: 'balance'},
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
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']]
              });

  $(document).on('change', '.type', function() {
    table.ajax.reload();
  });
</script>
@endsection
