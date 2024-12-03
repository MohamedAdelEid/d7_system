@extends('layouts.admin')

@section('title', trans('admin.dept_report'))

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0">{{ trans('admin.dept_report') }}</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('dashboard.home')}}">{{ trans('admin.Home') }}</a> / {{ trans('admin.dept_report') }}</li>
            </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!--start filter-->
    @include('Dashboard.reports.dept_filter')
    <!--end filter-->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="card">
            <div class="card-header">
          <!-- /.card-header -->
          <div class="card-body">
            <table class="table table-bordered table-striped data-table">
                <thead>
                <tr>
                    <th>{{ trans('admin.code') }}</th>
                    <th>{{ trans('admin.name') }}</th>
                    <th>{{ trans('admin.type') }}</th>
                    <th>{{ trans('admin.phone') }}</th>
                    <th>{{ trans('admin.balance') }}</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
                <tfoot>
                  <tr>
                      <th colspan="4" style="text-align:right">{{ trans('admin.total') }}:</th>
                      <th id="total-balance"></th>
                  </tr>
              </tfoot>
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
        "url": "{{ route('dashboard.reports.dept.report') }}",
        "data": function (d) {
          d.type = $('.type').val();
        }
      },
      columns: [
          {data: 'code', name: 'code'},   
          {data: 'name', name: 'name'},
          {data: 'type', name: 'type', render: function(data) {
              let colorClass, text;
              switch (data) {
                  case 'supplier':
                      colorClass = 'btn-primary';
                      text = 'مورد';
                      break;
                  case 'customer':
                      colorClass = 'btn-success';
                      text = 'عميل';
                      break;
                  default:
                      colorClass = 'btn-secondary';
                      text = data;
              }
              return '<span class="btn btn-m ' + colorClass + '" style="white-space: nowrap;">' + text + '</span>';
          }},
          {data: 'phone', name: 'phone'},
          {data: 'balance', name: 'balance'},
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

  $(document).on('change', '.type', function() {
      table.ajax.reload(function(json) {
          $('#total-balance').text(json.total);
      });
  });
</script>

@endsection
