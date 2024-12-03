@extends('layouts.admin')
@section('title', trans('admin.purchases'))
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
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('admin.purchases') }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">{{ trans('admin.Home') }}</a> /
                            {{ trans('admin.purchases') }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    @include('Dashboard.purchases.filter')
    <!-- Main content -->
    <section class="content">
        <div id="print-section">
        </div>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    @if (auth('user')->user()->has_permission('create-purchases'))
                        <a href="{{ route('dashboard.purchases.create') }}" type="button"
                            class="btn btn-info">{{ trans('admin.Add') }}</a>
                    @else
                        <a href="#" type="button" class="btn btn-info disabled">{{ trans('admin.Add') }}</a>
                    @endif
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            
                <!-- Success Message -->
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                    <table class="table table-bordered table-striped data-table responsive">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('admin.ref_no') }}</th>
                                <th>{{ trans('admin.supplier') }}</th>
                                <th>{{ trans('admin.phone') }}</th>
                                <th>{{ trans('admin.branch') }}</th>
                                <th>{{ trans('admin.payment_status') }}</th>
                                <th>{{ trans('admin.Delivery-Status') }}</th>
                                <th>{{ trans('admin.paid_from_transaction') }}</th>
                                <th>{{ trans('admin.remaining_amount') }}</th>
                                <th>{{ trans('admin.total') }}</th>
                                <th>{{ trans('admin.Created by') }}</th>
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
                "url": "{{ route('dashboard.purchases.index') }}",
                "data": function(d) {
                    d.branch_id = $('#branch_id').val();
                    d.supplier_id = $('#supplier_id').val();
                    d.payment_status = $('#payment_status').val();
                    d.date_from = $('#date_from').val();
                    d.date_to = $('#date_to').val();
                    d.created_by = $('#created_by').val();
                }
            },
            createdRow: function(row, data, dataIndex) {
                // Make all cells in the row clickable except the first and last one (actions column)
                $(row).children('td:not(:first-child):not(:last-child)').addClass('fire-popup')
                    .attr('data-target', '#modal-default-big')
                    .attr('data-toggle', 'modal')
                    .attr('data-url', data.route)
                    .css('cursor', 'pointer');
            },

            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'ref_no',
                    name: 'ref_no',
                    render: function(data, type, row) {
                        if (row.has_return_quantity) {
                            return data + ' <i class="fas fa-exchange-alt text-danger" title="Has Return"></i>';
                        }
                        return data;
                    }
                },
                {
                    data: 'supplier_name',
                    name: 'supplier_name'
                },
                {
                    data: 'supplier_phone',
                    name: 'supplier_phone'
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
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
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

        $(document).on('change', '#branch_id, #supplier_id, #payment_status, #date_from, #date_to, #created_by',
        function() {
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
