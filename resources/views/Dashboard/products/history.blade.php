@extends('layouts.admin')

@section('title', trans('admin.history'))

@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row align-items-center mb-4">
                <div class="col-sm-6">
                    <h1 class="m-0 text-primary">{{ trans('admin.history') }}</h1>
                </div>
                <div class="col-sm-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb float-sm-right bg-transparent p-0 mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}"
                                    class="text-primary">{{ trans('admin.Home') }}</a></li>
                            <li class="breadcrumb-item active">{{ trans('admin.history') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Left Sidebar -->

                <!-- Filter Card -->
                <div class="col-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-filter me-2"></i>
                                {{ trans('admin.filter') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="text-muted small mb-2">{{ trans('admin.branch') }}</label>
                                <select name="branch_id" class="form-select form-control-lg" id="branch_id">
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <!-- Product Search Card -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-search me-2"></i>
                                {{ trans('admin.search_products') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="input-group">
                                <input type="text" id="search" class="form-control form-control-lg"
                                    placeholder="ابحث عن المنتج ...." autocomplete="off">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <div class="search-results mt-3">
                                <ul id="result-list" class="list-group list-group-flush"></ul>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <div class="row">
                <!-- Main Content Area -->
                <div class="col-lg-12">
                    <!-- Product Info Card -->
                    <div class="card shadow-sm mb-4" id="statistics">
                        <div class="card-header bg-primary p-3">
                            <div class="d-flex align-items-center">
                                <img src="{{ $product->getImage() }}" class="rounded-circle me-3"
                                    alt="{{ $product->name }}" style="width: 50px; height: 50px; object-fit: cover;">
                                <h3 class="card-title text-white mb-0 mr-2 ">{{ $product->name }}</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <!-- Statistics Grid -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="border rounded-lg p-3 h-100 bg-light">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-chart-line text-primary me-2"></i>
                                            <h6 class="text-primary mb-0">{{ trans('admin.total_sales') }}</h6>
                                        </div>
                                        <p class="h3 mb-0" data-statistic="total_sales">{{ $statistics['total_sales'] }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <div class="border rounded-lg p-3 h-100 bg-light">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-shopping-cart text-success me-2"></i>
                                            <h6 class="text-success mb-0">{{ trans('admin.total_purchase') }}</h6>
                                        </div>
                                        <p class="h3 mb-0" data-statistic="total_purchase">
                                            {{ $statistics['total_purchase'] }}</p>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <div class="border rounded-lg p-3 h-100 bg-light">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-times-circle text-danger me-2"></i>
                                            <h6 class="text-danger mb-0">{{ trans('admin.total_spoiled') }}</h6>
                                        </div>
                                        <p class="h3 mb-0" data-statistic="total_spoiled">
                                            {{ $statistics['total_spoiled'] }}</p>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <div class="border rounded-lg p-3 h-100 bg-light">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-exchange-alt text-info me-2"></i>
                                            <h6 class="text-info mb-0">{{ trans('admin.total_transfer') }}</h6>
                                        </div>
                                        <p class="h3 mb-0" data-statistic="total_transfer">
                                            {{ $statistics['total_transfer'] }}</p>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <div class="border rounded-lg p-3 h-100 bg-light">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-undo text-warning me-2"></i>
                                            <h6 class="text-warning mb-0">{{ trans('admin.total_return_purchase') }}</h6>
                                        </div>
                                        <p class="h3 mb-0" data-statistic="total_return_purchase">
                                            {{ $statistics['total_return_purchase'] }}</p>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <div class="border rounded-lg p-3 h-100 bg-light">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-reply text-dark me-2"></i>
                                            <h6 class="text-dark mb-0">{{ trans('admin.total_return_sales') }}</h6>
                                        </div>
                                        <p class="h3 mb-0" data-statistic="total_return_sales">
                                            {{ $statistics['total_return_sales'] }}</p>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <div class="border rounded-lg p-3 h-100 bg-light">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-box-open text-secondary me-2"></i>
                                            <h6 class="text-secondary mb-0">{{ trans('admin.open_stock') }}</h6>
                                        </div>
                                        <p class="h3 mb-0" data-statistic="open_stock">{{ $statistics['open_stock'] }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="border rounded-lg p-3 h-100 bg-light">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-box-open text-secondary me-2"></i>
                                            <h6 class="text-secondary mb-0">{{ trans('admin.quantity_available_now') }}</h6>
                                        </div>
                                        <p class="h3 mb-0" data-statistic="quantity">{{ $statistics['quantity'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                   
                </div>
            </div>
            <div class="row">
                 <!-- History Table Card -->
                 <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i>
                            {{ trans('admin.history') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover data-table">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ trans('admin.Created at') }}</th>
                                    <th>{{ trans('admin.Created by') }}</th>
                                    <th>{{ trans('admin.contact') }}</th>
                                    <th>{{ trans('admin.ref_no') }}</th>
                                    <th>{{ trans('admin.type') }}</th>
                                    <th>{{ trans('admin.change_quantity') }}</th>
                                    <th>{{ trans('admin.change_quantity_by_subunit') }}</th>
                                    <th>{{ trans('admin.quantity') }}</th>
                                    <th>{{ trans('admin.quantity_by_sub_unit') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script type="text/javascript">
    
        $(document).ready(function() {
            // Initialize DataTable and other existing code...
         
            // Add data-product-id to the statistics div
            const productId = {{ $product_id }};
            $('#statistics').attr('data-product-id', productId);

            // Update statistics when branch changes
            $('#branch_id').on('change', function() {
                const branchId = $(this).val();

                // Show loading state
                $('#statistics .h3').html('<i class="fas fa-spinner fa-spin"></i>');

                // Update statistics
                $.ajax({
                    url: `/dashboard/products/${productId}/statistics/${branchId}`,
                    method: 'GET',
                    success: function(data) {
                        Object.keys(data).forEach(key => {
                            $(`[data-statistic="${key}"]`).text(data[key]);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating statistics:', error);
                        // Show error state
                        $('#statistics .h3').text('Error loading data');
                    }
                });

                // Reload DataTable
                $('.data-table').DataTable().ajax.reload();
            });
        });

        $(document).ready(function() {
            // Initialize Select2
            $('.form-select').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Initialize DataTable with custom styling
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('dashboard.products.history', $product_id) }}",
                    "data": function(d) {
                        d.branch_id = $('#branch_id').val();
                    }
                },
                columns: [{
                        data: 'created_at',
                        name: 'created_at',
                        orderable: false
                    },
                    {
                        data: 'created_by',
                        name: 'created_by',
                        orderable: false
                    },
                    {
                        data: 'contact_name',
                        name: 'contact_name',
                        render: function(data, type, row) {
                            return data ?? '-';
                        },
                        orderable: false
                    },
                    {
                        data: 'ref_no',
                        name: 'ref_no',
                        orderable: false
                    },
                    {
                        data: 'type',
                        name: 'type',
                        orderable: false,
                        render: function(data, type, row) {
                            // Add badge styling to type column
                            let badgeClass = 'badge ';
                            switch (data.toLowerCase()) {
                                case 'sale':
                                    badgeClass += 'bg-primary';
                                    break;
                                case 'purchase':
                                    badgeClass += 'bg-success';
                                    break;
                                case 'return':
                                    badgeClass += 'bg-warning';
                                    break;
                                default:
                                    badgeClass += 'bg-secondary';
                            }
                            return '<span class="' + badgeClass + '">' + data + '</span>';
                        }
                    },
                    {
                        data: 'change_quantity_string',
                        name: 'change_quantity_string',
                        orderable: false,
                        render: function(data, type, row) {
                            // Add color based on positive/negative value
                            let colorClass = parseFloat(data) >= 0 ? 'text-success' : 'text-danger';
                            return '<span class="' + colorClass + ' fw-bold">' + data + '</span>';
                        }
                    },
                    {
                        data: 'change_quantity_string_by_subunit',
                        name: 'change_quantity_string_by_subunit',
                        orderable: false,
                        render: function(data, type, row) {
                            // Add color based on positive/negative value
                            let colorClass = parseFloat(data) >= 0 ? 'text-success' : 'text-danger';
                            return '<span class="' + colorClass + ' fw-bold">' + data + '</span>';
                        }
                    },
                    {
                        data: 'quantity',
                        name: 'quantity',
                        orderable: false
                    },
                    {
                        data: 'quantity_by_subunit',
                        name: 'change_quantity_by_subunit',
                        orderable: false
                    },
                ],
                dom: '<"d-flex justify-content-between align-items-center mb-4"lB>rtip',
                buttons: [{
                        extend: 'collection',
                        text: '<i class="fas fa-download me-1"></i> Export',
                        className: 'btn btn-primary',
                        buttons: [{
                                extend: 'copy',
                                className: 'dropdown-item'
                            },
                            {
                                extend: 'excel',
                                className: 'dropdown-item'
                            },
                            {
                                extend: 'csv',
                                className: 'dropdown-item'
                            },
                            {
                                extend: 'pdf',
                                className: 'dropdown-item'
                            },
                            {
                                extend: 'print',
                                className: 'dropdown-item'
                            }
                        ]
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="fas fa-columns me-1"></i> Columns',
                        className: 'btn btn-secondary'
                    }
                ],
                language: {
                    search: "",
                    searchPlaceholder: "Search records..."
                },
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                order: [
                    [0, 'desc']
                ]
            });

            // Enhanced search functionality with debounce
            let searchTimeout;
            $('#search').on('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = $(this).val();

                searchTimeout = setTimeout(function() {
                    if (searchTerm.length >= 1) {
                        searchProducts(searchTerm);
                    } else {
                        $('#result-list').empty();
                    }
                }, 300);
            });

            // Branch filter with loading state
            $(document).on('change', '#branch_id', function() {
                const $button = $(this).closest('.card').find('.btn-primary');
                $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

                table.ajax.reload(function() {
                    $button.prop('disabled', false).html('<i class="fas fa-search"></i>');
                });
            });
        });

        function searchProducts(query) {
            $.ajax({
                url: '{{ route('dashboard.products.search') }}',
                type: 'GET',
                data: {
                    query: query
                },
                beforeSend: function() {
                    $('#result-list').html(
                        '<li class="list-group-item text-center"><i class="fas fa-spinner fa-spin"></i> Searching...</li>'
                    );
                },
                success: function(response) {
                    $('#result-list').empty();

                    if (response.length > 0) {
                        $.each(response, function(index, product) {
                            $('#result-list').append(`
                            <li class="list-group-item border-0 py-2">
                                <a href="/dashboard/products/${product.id}/history" 
                                   class="d-flex align-items-center text-dark text-decoration-none hover-bg-light p-2 rounded">
                                    <i class="fas fa-box me-2 text-primary"></i>
                                    <span>${product.name}</span>
                                </a>
                            </li>
                        `);
                        });
                    } else {
                        $('#result-list').append(`
                        <li class="list-group-item border-0 py-3 text-center text-muted">
                            <i class="fas fa-search me-2"></i>
                            {{ trans('admin.no_product_found') }}
                        </li>
                    `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error searching products:', error);
                    $('#result-list').html(`
                    <li class="list-group-item border-0 py-3 text-center text-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Error searching products
                    </li>
                `);
                }
            });
        }
    </script>
  
@endsection
