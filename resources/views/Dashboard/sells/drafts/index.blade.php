@extends('layouts.admin')

@section('title', trans('admin.drafts'))

@section('style')
    <style>
        #print-section {
            display: none;
        }

        @media print {
            body * {
                visibility: hidden;

            }

            #print-section * {
                visibility: visible;

            }

            #print-section {
                display: block;

            }
        }
    </style>
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('admin.drafts') }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">{{ trans('admin.Home') }}</a> /
                            {{ trans('admin.drafts') }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <!-- Main content -->
    <section class="content">
        <div id="print-section">

        </div>

        <div class="container-fluid">
            <div class="card">
           
                <!-- /.card-header -->
                <div class="card-body">
                    <div style="max-width: 100%; overflow-x: auto;">
                        <table class="table table-bordered table-striped data-table responsive">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('admin.ref_no') }}</th>
                                    <th>{{ trans('admin.contact') }}</th>
                                    <th>{{ trans('admin.phone') }}</th>
                                    <th>{{ trans('admin.branch') }}</th>
                                    <th>{{ trans('admin.total') }}</th>
                                    <th>{{ trans('admin.city') }}</th>
                                    <th>{{ trans('admin.government') }}</th>
                                    <th>{{ trans('admin.Created by') }}</th>
                                    <th>{{ trans('admin.Created at') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
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
                "url": "{{ route('dashboard.sells.drafts.index') }}",
                "data": function(d) {
                    d.contact_id = $('#contact_id').val();
                    d.branch_id = $('#branch_id').val();
                    d.date_from = $('#date_from').val();
                    d.date_to = $('#date_to').val();
                    d.created_by = $('#created_by').val();
                }
            },
  
            columnDefs: [
                {
                    targets: 1,
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).on('click', function() {
                    window.location.href = rowData.route; // Redirect to the route URL
                })
                            .attr('style', 'cursor: pointer');
                    }
                },
                {
                    targets: 2,
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).on('click', function() {
                    window.location.href = rowData.route; // Redirect to the route URL
                })
                            .attr('style', 'cursor: pointer');
                    }
                },
                {
                    targets: 3,
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).on('click', function() {
                    window.location.href = rowData.route; // Redirect to the route URL
                })
                            .attr('style', 'cursor: pointer');
                    }
                },
                {
                    targets: 4,
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).on('click', function() {
                    window.location.href = rowData.route; // Redirect to the route URL
                })
                            .attr('style', 'cursor: pointer');
                    }
                },
                {
                    targets: 5,
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).on('click', function() {
                    window.location.href = rowData.route; // Redirect to the route URL
                })
                            .attr('style', 'cursor: pointer');
                    }
                },
                {
                    targets: 6,
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).on('click', function() {
                    window.location.href = rowData.route; // Redirect to the route URL
                })
                            .attr('style', 'cursor: pointer');
                    }
                },
                {
                    targets: 7,
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).on('click', function() {
                    window.location.href = rowData.route; // Redirect to the route URL
                })
                            .attr('style', 'cursor: pointer');
                    }
                },
                {
                    targets: 8,
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).on('click', function() {
                    window.location.href = rowData.route; // Redirect to the route URL
                })
                            .attr('style', 'cursor: pointer');
                    }
                },
            ],
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'ref_no',
                    name: 'ref_no'
                },
                {
                    data: 'contact',
                    name: 'contact'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'branch_id',
                    name: 'branch_id'
                },
                {
                    data: 'total',
                    name: 'total'
                },
                {
                    data: 'government',
                    name: 'government'
                },
                {
                    data: 'city',
                    name: 'city',
                },
                {
                    data: 'created_by',
                    name: 'created_by'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function(data) {
                        return moment(data).format('h:mm YYYY-MM-DD');
                    }
                },

               
            ],

            dom: 'lBfrtip',
            buttons: [{
                    extend: 'copy',
                    exportOptions: {
                        search: 'none',
                        columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        search: 'none',
                        columns: ':visible'
                    }
                },
                {
                    extend: 'csv',
                    exportOptions: {
                        search: 'none',
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdf',
                    exportOptions: {
                        search: 'none',
                        columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        search: 'none',
                        columns: ':visible'
                    }
                },
                {
                    extend: 'colvis',
                    exportOptions: {
                        search: 'none',
                        columns: ':visible'
                    }
                },
            ],
            // "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, 'All'] ]
        });

        $(document).on('change', '#branch_id, #contact_id, #date_from, #date_to, #created_by, #payment_status', function() {
            table.ajax.reload();
        });
    </script>
    <script>
        $(document).ready(function() {

            console.log($('.print-invoice'));

            $(document).on('click', '.print-invoice', function(e) {

                var route = $(this).attr('href');
                e.preventDefault();
                console.log(route);
                $.ajax({
                    url: route,
                    type: 'get',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#print-section').html(response);
                        setTimeout(() => {
                            window.print();
                        }, 1000);

                        console.log("AJAX request successful:", response);

                    },
                    createdRow: function(row, data, dataIndex) {
                        $(row).css('cursor', 'pointer'); // Change cursor to pointer
                        $(row).on('click', function() {
                            window.location.href = data.route; // Assuming `route` column is the URL
                        });
                    },

                    error: function(xhr, status, error) {
                        console.log("AJAX request failed:", error);
                    }

                });
            });
        });
    </script>
@endsection
