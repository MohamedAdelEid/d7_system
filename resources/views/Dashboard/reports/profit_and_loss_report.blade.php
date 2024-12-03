@extends('layouts.admin')

@section('title', trans('admin.profit_and_loss_report'))

@section('styles')
    <style>
        .report-card {
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .filter-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .total-section {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .profit-positive {
            color: #28a745;
        }

        .profit-negative {
            color: #dc3545;
        }
    </style>
@endsection

@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('admin.profit_and_loss_report') }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.home') }}">{{ trans('admin.Home') }}</a> /
                            {{ trans('admin.profit_and_loss_report') }}
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <div class="container-fluid">
        <!-- Filters -->
        <div class="row">
            <div class="col-12">
                <div class="card filter-section">
                    <div class="card-body">
                        <form id="reportFilterForm" method="GET">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('admin.date_range') }}</label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" name="start_date">
                                            <input type="date" class="form-control" name="end_date">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('admin.branch') }}</label>
                                        <select class="form-control" name="branch_id">
                                            <option value="">{{ trans('admin.all_branches') }}</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 align-self-end">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        {{ trans('admin.filter') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Content -->
        <div class="row">
            <div class="col-12">
                <div class="card report-card">
                    <div class="card-body">
                        <!-- Sales Section -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <table class="table">
                                    <tr>
                                        <td>{{ trans('admin.total_purchases') }}</td>
                                        <td class="text-right">£ {{ number_format($totla_purchase, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('admin.total_sales') }}</td>
                                        <td class="text-right">£ {{ number_format($total_sales, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('admin.total_spoiled_products') }}</td>
                                        <td class="text-right">£ {{ number_format($total_price_of_spoiled_stock, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('admin.total_expenses') }}</td>
                                        <td class="text-right">£ {{ number_format($total_expenses, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('admin.total_discounts_in_sales') }}</td>
                                        <td class="text-right">£ {{ number_format($total_discount_in_sales, 2) }}</td>
                                    </tr>

                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table">
                                    <tr>
                                        <td>{{ trans('admin.total_purchases_returns') }}</td>
                                        <td class="text-right">£ {{ number_format($totla_purchase_returns, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('admin.total_sales_returns') }}</td>
                                        <td class="text-right">£ {{ number_format($total_sales_returns, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('admin.total_opening_balance_for_customers') }}</td>
                                        <td class="text-right">£
                                            {{ number_format($total_opening_balance_for_customers, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('admin.total_opening_balance_for_suppliers') }}</td>
                                        <td class="text-right">£
                                            {{ number_format($total_opening_balance_for_suppliers, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('admin.total_discounts_in_purchases') }}</td>
                                        <td class="text-right">£ {{ number_format($total_discount_in_purchases, 2) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Summary Section -->

                    </div>

                </div>
            </div>
        </div>
        <div class="total-section bg-light p-4 rounded shadow">
            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark">{{ trans('admin.total_profit') }}</h5>
                        <h4 class="text-success">£ {{ number_format($total_profit, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <!-- Detailed Analysis Tabs -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#products">
                                    {{ trans('admin.profit_by_product') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#categories">
                                    {{ trans('admin.profit_by_category') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#brands">
                                    {{ trans('admin.profit_by_brand') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#customers">
                                    {{ trans('admin.profit_by_customer') }}
                                </a>
                            </li>
                           
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#branches">
                                    {{ trans('admin.profit_by_branch') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="products">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('admin.product') }}</th>
                                            <th class="text-start">{{ trans('admin.total_profit') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($products as $product)
                                            <tr>
                                                <td>{{ $product->name }}</td>
                                                <td class="text-start">{{ number_format($total_profit_by_product[$product->id], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- For branches tab -->
                            <div class="tab-pane fade" id="branches">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('admin.branch') }}</th>
                                            <th class="text-start">{{ trans('admin.total_profit') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($branches as $branch)
                                            <tr>
                                                <td>{{ $branch->name }}</td>
                                                <td class="text-start">{{ number_format($total_profit_by_branch[$branch->id], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- For customers tab -->
                            <div class="tab-pane fade" id="customers">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('admin.customer') }}</th>
                                            <th class="text-start">{{ trans('admin.total_profit') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($customers as $customer)
                                            <tr>
                                                <td>{{ $customer->name }}</td>
                                                <td class="text-start">{{ number_format($total_profit_by_customer[$customer->id], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- For categories tab -->
                            <div class="tab-pane fade" id="categories">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('admin.category') }}</th>
                                            <th class="text-start">{{ trans('admin.total_profit') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($categories as $category)
                                            <tr>
                                                <td>{{ $category->name }}</td>
                                                <td class="text-start">{{ number_format($total_profit_by_category[$category->id], 2) }}</td>
                                            </tr>
                                        @endforeach
                                        <!-- Will be populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- For brands tab -->
                            <div class="tab-pane fade" id="brands">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('admin.brand') }}</th>
                                            <th class="text-start">{{ trans('admin.total_profit') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($brands as $brand)
                                            <tr>
                                                <td>{{ $brand->name }}</td>
                                                <td class="text-start">{{ number_format($total_profit_by_brand[$brand->id], 2) }}</td>
                                            </tr>
                                        @endforeach
                                        <!-- Will be populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialize date pickers
            $('input[type="date"]').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });

           

        });
    </script>
@endsection
