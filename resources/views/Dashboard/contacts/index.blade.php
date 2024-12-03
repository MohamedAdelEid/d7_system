@extends('layouts.admin')

@section('title', trans('admin.contacts'))


@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0">{{ trans('admin.contacts') }}</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('dashboard.home')}}">{{ trans('admin.Home') }}</a> / {{ trans('admin.contacts') }}</li>
            </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!--start filter-->
    @include('Dashboard.contacts.filter')
    <!--end filter-->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header">
            @if (auth('user')->user()->has_permission('create-contacts'))
              <a href="{{route('dashboard.contacts.create')}}" type="button" class="btn btn-info">{{ trans('admin.Add') }}</a>
            @else
              <a href="#" type="button" class="btn btn-info disabled">{{ trans('admin.Add') }}</a>
            @endif
            {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
              استيراد
            </button>
            <a href="{{asset('files/template.xlsx')}}" class="btn btn-success" download >
              تحميل نموذج
            </a>
             --}}
            {{-- <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">استيراد جهات اتصال</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form action="{{ route('dashboard.contacts.import.contacts') }}" method="POST" enctype="multipart/form-data">
                  <div class="modal-body">
                      @csrf
                      <div class="form-group">
                          <label for="file">اختار ملف </label>
                          <input type="file" name="file" class="form-control">
                      </div>
                      
                  
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">استيراد</button>
                  </div>
                </form>
                </div>
              </div>
            </div> --}}
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <table class="table table-bordered table-striped data-table">
                <thead>
                <tr>
                    <th>{{ trans('admin.code') }}</th>
                    <th>{{ trans('admin.name') }}</th>
                    <th>{{ trans('admin.type') }}</th>
                    <th>{{ trans('admin.phone') }}</th>
                    <th>{{ trans('admin.address') }}</th>
                    <th>{{ trans('admin.balance') }}</th>
                    <th>{{ trans('admin.credit_limit') }}</th>
                    <th>{{ trans('admin.sales_segment_id') }}</th>
                    <th>{{ trans('admin.is_active') }}</th>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script type="text/javascript">
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          "url": "{{ route('dashboard.contacts.index') }}",
          "data": function ( d ) {
            d.type = $('.type').val();
            d.sales_segment_id = $('.sales_segment_id').val();
          }
        },
        createdRow: function(row, data, dataIndex) {
        $(row).children('td:not(:last-child)').addClass('fire-popup')
            .attr('data-target', '#modal-default-big')
            .attr('data-toggle', 'modal')
            .attr('data-url', data.route)
            .css('cursor', 'pointer');
        },
        columns: [
            {data: 'code', name: 'code'},
            {data: 'name', name: 'name'},
            {data: 'type', name: 'type'},
            {data: 'phone', name: 'phone'},
            {data: 'address', name: 'address'},
            {data: 'balance', name: 'balance'},
            {data: 'credit_limit', name: 'credit_limit', render: function(data, type, row) {
                return data === null ? 'لا يوجد' : data;
            }},
            {data: 'sales_segment_id', name: 'sales_segment_id'},
            {data: 'is_active', name: 'is_active'},
            {data: 'created_at', name: 'created_at'},
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
  $(document).on('change', '.sales_segment_id', function() {
    table.ajax.reload();
  });
</script>
@endsection
