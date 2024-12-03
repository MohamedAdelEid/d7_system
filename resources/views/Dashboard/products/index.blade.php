@extends('layouts.admin')

@section('title', trans('admin.products'))
@section('style')
    <style>
        table.dataTable>thead .sorting_disabled:before,
        table.dataTable>thead .sorting_disabled:after {
            opacity: 1;
            display: none !important;
        }

        table.dataTable>thead .sorting_disabled:after,
        table.dataTable>thead .sorting_disabled:after,
        table.dataTable>thead .sorting_disabled:after,
        table.dataTable>thead .sorting_disabled:after,
        table.dataTable>thead .sorting_disabled:after {
            right: .5em;
            content: "↓";
            display: none;
        }
    </style>
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('admin.products') }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">{{ trans('admin.Home') }}</a> /
                            {{ trans('admin.products') }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <!--start filter-->
    @include('Dashboard.products.filter')
    <!--end filter-->
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    @if (auth('user')->user()->has_permission('create-products'))
                        <a href="{{ route('dashboard.products.create') }}" type="button"
                            class="btn btn-info">{{ trans('admin.Add') }}</a>
                    @else
                        <a href="#" type="button" class="btn btn-info disabled">{{ trans('admin.Add') }}</a>
                    @endif
                    @if (auth('user')->user()->has_permission('import-products'))
                        <a href="{{ route('dashboard.products.import') }}" type="button"
                            class="btn btn-info">{{ trans('admin.import') }}</a>
                    @else
                        <a href="#" type="button" class="btn btn-info disabled">{{ trans('admin.import') }}</a>
                    @endif

                    <button id="bulk-edit-btn" class="btn btn-warning"
                        style="display: none;">{{ trans('admin.bulk_edit') }}</button>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table class="table table-bordered table-striped data-table responsive">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all-checkbox"></th>
                                <th>{{ trans('admin.sku') }}</th>
                                <th>{{ trans('admin.image') }}</th>
                                <th>{{ trans('admin.name') }}</th>
                                <th>{{ trans('admin.branchs') }}</th>
                                <th>{{ trans('admin.main unit') }}</th>
                                <th>{{ trans('admin.qty_available_by_main_unit') }}</th>
                                <th>{{ trans('admin.qty_available_by_sub_unit') }}</th>
                                @if (auth('user')->user()->has_permission('show-sell-price-products'))
                                    <th>{{ trans('admin.sell_price') }}</th>
                                @endif
                                @if (auth('user')->user()->has_permission('show-purchase-price-products'))
                                    <th>{{ trans('admin.purchase_price') }}</th>
                                @endif
                                <th>{{ trans('admin.brand') }}</th>
                                <th>{{ trans('admin.category') }}</th>
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
    <!-- Modal -->
    <div class="modal fade" id="branches-modal" tabindex="-1" role="dialog" aria-labelledby="branches-modal-label"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="branches-modal-label">{{ trans('admin.branches') }} </h5>
                    <button type="button" onclick="$('#branches-modal').modal('hide')" class="close" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        onclick="$('#branches-modal').modal('hide')">{{ trans('admin.Close') }}</button>
                </div>
            </div>
        </div>
        <!-- /.Modal -->

    @endsection

    @section('script')
        <script type="text/javascript">
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('dashboard.products.index') }}",
                    "data": function(d) {
                        d.brand_id = $('#brand_id').val();
                        d.category_id = $('#category_id').val();
                        d.branch_id = $('#branch_id').val();
                    }
                },
                createdRow: function(row, data, dataIndex) {
                    // Make all cells in the row clickable except the last one (actions column) and the "Branches" column
                    $(row).children('td:not(:last-child)').not(':nth-child(5), :nth-child(1)').addClass(
                            'fire-popup')
                        .attr('data-target', '#modal-default-big')
                        .attr('data-toggle', 'modal')
                        .attr('data-url', data.route)
                        .css('cursor', 'pointer');
                },

                columns: [{
                        data: 'bulk_edit',
                        name: 'bulk_edit',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'sku',
                        name: 'sku'
                    },
                    {
                        data: 'image',
                        name: 'image'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'branches',
                        name: 'branches',
                        render: function(data, type, row) {
                            return '<a href="#" class="view-branches" data-id="' + row.id + '">' + row
                                .branches + '</a>';
                        }
                    },
                    {
                        data: 'unit',
                        name: 'unit',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'qty_available_by_main_unit',
                        name: 'qty_available_by_main_unit',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'qty_available_by_sub_unit',
                        name: 'qty_available_by_sub_unit',
                        orderable: false,
                        searchable: false
                    },
                    @if (auth('user')->user()->has_permission('show-sell-price-products'))
                    {
                        data: 'sell_price',
                        name: 'sell_price',
                        render: function(data, type, row) {
                            return data ? data : 'N/A';
                        }
                    },
                    @endif
                    @if (auth('user')->user()->has_permission('show-purchase-price-products'))
                    {
                        data: 'purchase_price',
                        name: 'purchase_price',
                        render: function(data, type, row) {
                            return data ? data : 'N/A';
                        }
                    },
                    @endif
                    {
                        data: 'brand',
                        name: 'brand',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'category',
                        name: 'category',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
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
                            modifier: {
                                page: 'all',
                                search: 'none'
                            }
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            modifier: {
                                page: 'all',
                                search: 'none'
                            }
                        }
                    },
                    {
                        extend: 'csv',
                        exportOptions: {
                            modifier: {
                                page: 'all',
                                search: 'none'
                            }
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            modifier: {
                                page: 'all',
                                search: 'none'
                            }
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            modifier: {
                                page: 'all',
                                search: 'none'
                            }
                        }
                    },
                    {
                        extend: 'colvis',
                        exportOptions: {
                            modifier: {
                                page: 'all',
                                search: 'none'
                            }
                        }
                    },
                ],
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'All']
                ]
            });

            $(document).on('change', '#brand_id, #category_id', function() {
                table.ajax.reload();
            });

            $(document).on('change', '#branch_id', function() {
                table.ajax.reload();
            });

            // Bulk edit functionality
            $('#select-all-checkbox').on('click', function() {
                $('.bulk-edit-checkbox').prop('checked', this.checked);
                updateBulkEditButton();
            });

            $(document).on('change', '.bulk-edit-checkbox', function() {
                updateBulkEditButton();
            });

            function updateBulkEditButton() {
                if ($('.bulk-edit-checkbox:checked').length > 0) {
                    $('#bulk-edit-btn').show();
                } else {
                    $('#bulk-edit-btn').hide();
                }
            }

            $('#bulk-edit-btn').on('click', function() {
                var selectedIds = $('.bulk-edit-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();
           
                // Redirect to bulk edit page with selected IDs
                window.location.href = "{{ route('dashboard.products.bulkEdit') }}?ids=" + selectedIds.join(',');
            });

            $(document).on('click', '.view-branches', function(e) {
                e.preventDefault();
                var productId = $(this).data('id');
                $.ajax({
                    url: "{{ route('dashboard.products.branches', ['product_id' => ':productId']) }}".replace(
                        ':productId', productId),
                    method: 'GET',
                    success: function(response) {
                        $('#branches-modal .modal-body').html(response);
                        $('#branches-modal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching branches:", error);
                        $('#branches-modal .modal-body').html(
                            '<p>Error fetching branches. Please try again.</p>');
                        $('#branches-modal').modal('show');
                    }
                });
            });
        </script>
    @endsection
