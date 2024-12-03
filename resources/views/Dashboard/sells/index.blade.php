@extends('layouts.admin')

@section('title', trans('admin.sells'))

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
            table {
                table-layout: fixed
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
                    <h1 class="m-0">{{ trans('admin.sells') }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">{{ trans('admin.Home') }}</a> /
                            {{ trans('admin.sells') }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    @include('Dashboard.sells.filter')
    <!-- Main content -->
    <section class="content">
        <div id="print-section">

        </div>

        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    @if (auth('user')->user()->has_permission('create-sells'))
                        <a href="{{ route('dashboard.sells.create') }}" type="button"
                            class="btn btn-info">{{ trans('admin.Add') }}</a>
                    @else
                        <a href="#" type="button" class="btn btn-info disabled">{{ trans('admin.Add') }}</a>
                    @endif
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div>
                        <table class="table table-bordered table-striped data-table responsive">
                            <thead>
                                <tr>
                                    <th>{{ trans('admin.Actions') }}</th>
                                    <th>{{ trans('admin.ref_no') }}</th>
                                    <th>{{ trans('admin.contact') }}</th>
                                    <th>{{ trans('admin.phone') }}</th>
                                    <th>{{ trans('admin.branch') }}</th>
                                    <th>{{ trans('admin.payment_status') }}</th>
                                    <th>{{ trans('admin.Delivery-Status') }}</th>
                                    <th>{{ trans('admin.paid_from_transaction') }}</th>
                                    <th>{{ trans('admin.remaining_amount') }}</th>
                                    <th>{{ trans('admin.total') }}</th>
                                    <th>{{ trans('admin.city') }}</th>
                                    <th>{{ trans('admin.government') }}</th>
                                    <th>{{ trans('admin.transaction_from') }}</th>
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
            responsive: false,
            scrollX: true,    // Enable horizontal scroll
            scrollCollapse: true, // Allow table to collapse if data is less
            paging: true, // Enable pagination
            ajax: {
                "url": "{{ route('dashboard.sells.index') }}",
                "data": function(d) {
                    d.contact_id = $('#contact_id').val();
                    d.branch_id = $('#branch_id').val();
                    d.date_from = $('#date_from').val();
                    d.date_to = $('#date_to').val();
                    d.created_by = $('#created_by').val();
                    d.payment_status = $('#payment_status').val();
                }
            },
            createdRow: function(row, data, dataIndex) {
        // Add success background color if payment status is final
        if (data.payment_status === 'final') {
                    $(row).css('background-color', 'lightgreen').css('box-shadow', '0 0 20px lightgreen');
                }
            },
            columnDefs: [
                {
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
                {
                    targets: 7,
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).addClass('fire-popup')
                            .attr('data-target', '#modal-default-big')
                            .attr('data-toggle', 'modal')
                            .attr('data-url', rowData.route)
                            .attr('style', 'cursor: pointer');
                    }
                },
                {
                    targets: 8,
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
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'ref_no',
                    name: 'ref_no',
                    render: function(data, type, row) {
                        if (row.return_quantity > 0) {
                            return '<span class="ref-no-with-return">' + data + '</span>';
                        }
                        return data;
                    }
                },
                {
                    data: 'contact_name',
                    name: 'contact_name'
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
                    data: 'payment_status',
                    name: 'payment_status',
                    render: function(data) {
                        let colorClass, text;
                        switch (data) {
                            case 'due':
                                colorClass = 'btn-primary';
                                text = 'غير مدفوع';
                                break;
                            case 'final':
                                colorClass = 'btn-success';
                                text = 'مدفوع';
                                break;
                            case 'partial':
                                colorClass = 'btn-warning';
                                text = 'جزئي';
                                break;
                            default:
                                colorClass = 'btn-secondary';
                                text = data;
                        }
                        return '<span class="btn btn-sm ' + colorClass + '" style="white-space: nowrap;">' +
                            text + '</span>';
                    }
                },
                {
                    data: 'delivery_status',
                    name: 'delivery_status',
                    render: function(data) {
                        let colorClass, text;
                        switch (data) {
                            case 'ordered':
                                colorClass = 'btn-primary';
                                text = 'تم الطلب';
                                break;
                            case 'shipped':
                                colorClass = 'btn-warning';
                                text = 'تم الشحن';
                                break;
                            case 'delivered':
                                colorClass = 'btn-success';
                                text = 'تم التوصيل';
                                break;
                            default:
                                colorClass = 'btn-secondary';
                                text = data;
                        }
                        return '<span class="btn btn-sm ' + colorClass + '" style="white-space: nowrap;">' +
                            text + '</span>';
                    }

                },
                {
                    data: 'paid_from_transaction',
                    name: 'paid_from_transaction'
                },
                {
                    data: 'remaining_amount',
                    name: 'remaining_amount'
                },
                {
                    data: 'total',
                    name: 'total'
                },
          
                {
                    data: 'contact.city.city_name_ar',
                    name: 'contact.city.city_name_ar',
                    defaultContent: 'N/A',
                },
                {
                    data: 'government',
                    name: 'government'
                },
                {
                    data: 'transaction_from',
                    name: 'transaction_from'
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
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']]
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
                    error: function(xhr, status, error) {
                        console.log("AJAX request failed:", error);
                    }

                });
            });
        });
    </script>
@endsection
