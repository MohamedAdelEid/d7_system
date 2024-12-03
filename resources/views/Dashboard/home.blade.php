@extends('layouts.admin')

@section('title', 'Home')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('admin.Dashboard') }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">{{ trans('admin.Home') }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            @include('Dashboard.filter')
            <div class="row">
                <!-- Sales related cards -->
                @if(auth('user')->user()->has_permission('read-total_sales-statistics'))
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $total_sales }}</h3>
                            <p>{{ trans('admin.total_sales') }}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                    </div>
                </div>
                @endif
           
                @if(auth('user')->user()->has_permission('read-total_paid_sales-statistics'))
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ $total_paid_sales }}</h3>
                            <p>{{ trans('admin.total_paid_sales') }}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-cash"></i>
                        </div>
                    </div>
                </div>
                @endif
                @if(auth('user')->user()->has_permission('read-total_partial_sell-statistics'))
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $total_partial_sell }}</h3>
                            <p>{{ trans('admin.total_partial_sell') }}</p>
                        </div>
                    </div>
                </div>
                @endif
                @if(auth('user')->user()->has_permission('read-total_unpaid_sales-statistics'))
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $total_unpaid_sales }}</h3>
                            <p>{{ trans('admin.total_unpaid_sales') }}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                    </div>
                </div>
                @endif
                <!-- ./col -->
               
                <!-- ./col -->
              
                
                <!-- ./col -->
            </div>
            <!-- /.row -->
            <div class="row">
                @if(auth('user')->user()->has_permission('read-total_sales_returns-statistics'))
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $total_sales_returns }}</h3>
                            <p>{{ trans('admin.total_sales_returns') }}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                    </div>
                </div>
                @endif
                <!-- ./col -->
                <!-- Purchase related cards -->
                @if(auth('user')->user()->has_permission('read-total_purchase-statistics'))
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $totla_purchase }}</h3>
                            <p>{{ trans('admin.total_purchase') }}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                    </div>
                </div>
                @endif
                <!-- ./col -->
                @if(auth('user')->user()->has_permission('read-total_paid_purchase-statistics'))
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ $total_paid_purchase }}</h3>
                            <p>{{ trans('admin.total_paid_purchase') }}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-cash"></i>
                        </div>
                    </div>
                </div>
                @endif
                <!-- ./col -->
                @if(auth('user')->user()->has_permission('read-total_partial_purchase-statistics'))
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $total_partial_purchase }}</h3>
                            <p>{{ trans('admin.total_partial_purchase') }}</p>
                        </div>
                    </div>
                </div>
                @endif
                <!-- ./col -->
                @if(auth('user')->user()->has_permission('read-total_paid_purchase-statistics'))
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ $totla_unpaid_purchase }}</h3>
                            <p>{{ trans('admin.totla_unpaid_purchase') }}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-cash"></i>
                        </div>
                    </div>
                </div>
                @endif
                <!-- ./col -->
                @if(auth('user')->user()->has_permission('read-total_purchase_returns-statistics'))
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ $totla_purchase_returns }}</h3>
                            <p>{{ trans('admin.totla_purchase_returns') }}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-refresh"></i>
                        </div>
                    </div>
                </div>
                @endif
                <!-- ./col -->
                
                @if(auth('user')->user()->has_permission('read-total_expenses-statistics'))
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-light">
                        <div class="inner">
                            <h3>{{ $total_expenses }}</h3>
                            <p>{{ trans('admin.total_expenses') }}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-wallet"></i>
                        </div>
                    </div>
                </div>
                @endif
                <!-- ./col -->
          
                @if(auth('user')->user()->has_permission('read-net_profit-statistics'))
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-dark">
                        <div class="inner">
                            <h3>{{ $net_profit }}</h3>
                            <p>{{ trans('admin.net_profit') }}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-clipboard"></i>
                        </div>
                    </div>
                </div>
                @endif
                <!-- ./col -->
            </div>
            <!-- /.row -->
         
            <!-- /.row -->

                <!-- ./col -->
            </div>
            <!-- /line chart -->
            <div class="row">
                @if(auth('user')->user()->has_permission('read-chart-statistics'))
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                                <h3 class="card-title">{{ trans('admin.chart_transactions') }}</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            {!! $chart->container() !!}
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                @endif
                <!-- /.col -->
            </div>
            <!-- /line chart -->
            <!-- /.row -->
            <div class="row">
                @if(auth('user')->user()->has_permission('read-total_profit-statistics'))
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ trans('admin.total_profit') }} : {{ $total_profit }}</h3>
                        </div>

                    </div>
                    <!-- /.card -->
                @endif
                @if(auth('user')->user()->has_permission('read-net_profit-statistics'))
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ trans('admin.net_profit') }} : {{ $net_profit }}</h3>
                        </div>
                        
                    </div>
                    <!-- /.card -->
                    </div>
                @endif
                <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- /.row -->
            @if(auth('user')->user()->has_permission('read-AlertedProducts-statistics'))
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ trans('admin.AlertedProducts') }}</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ trans('admin.ProductName') }}</th>
                                        <th>{{ trans('admin.BranchName') }}</th>
                                        <th>{{ trans('admin.QtyAvailable') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>{{ $product->Product?->name }}</td>
                                            <td>{{ $product->Branch?->name }}</td>
                                            <td>{{ $product->qty_available }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            <!-- /.row -->
            @endif
        </div><!-- /.container-fluid -->
    </section>

    <!-- /.content -->
@endsection
@section('script')
    <script src="{{ $chart->cdn() }}"></script>
    {{ $chart->script() }}

@endsection
