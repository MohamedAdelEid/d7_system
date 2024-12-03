@extends('layouts.invoice')

@section('title', trans('admin.invoice'))

<style>

.invoice-info, .client-info {
    width: 50%;
    float: left;
}
.invoice-info, .client-info {
    width: 50%;
    float: left;
}
.total {
    font-weight: bold;
    margin-top: 20px;
}
footer {
    display: none !important;
}
.preloader {
    display: none !important;
}
@media print {
    .content-header {
        margin-top: 100px;
    }
}
</style>

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header" style="margin-top: 50px;">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                  
                </div><!-- /.col -->
                <div class="col-sm-6">

                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <section class="content">
        <div class="container">
            <div class="row">
                <div class="invoice-info">
                    <p style="font-weight:bold;"> فاتوره رقم : {{ $transaction->ref_no }} </p>
                    <div class="" >
                        <img style="border-radius: 50%; width: 150px ; height: 150px" src="{{ asset('logo.png') }}" alt="">
                    </div>
                </div>
                <div class="client-info">
                    <p style="font-weight:bold;"> المورد: {{ $transaction->Contact->name }} </p>
                    <p style="font-weight:bold;"> الموبايل: {{ $transaction->Contact->phone }} </p>
                </div>
            </div>
            <div class="row" style="margin-top: 40px">
                <table class="table table-bordered table-striped data-table responsive">
                    <thead>
                        <tr>

                            <th>{{ trans('admin.product') }}</th>
                            <th>{{ trans('admin.quantity') }}</th>
                            <th>{{ trans('admin.price') }}</th>
                            <th>{{ trans('admin.total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transaction->TransactionPurchaseLines as $purchase)
                            <tr>
                                <td>{{ $purchase->product->name }}</td>
                                <td>{{ $purchase->quantity }} {{ $purchase->Unit->actual_name }}</td>
                                <td>{{ $purchase->unit_price }}</td>
                                <td>{{ $purchase->unit_price *  $purchase->quantity}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div>
                <div class="total">
                    الاجمالي  : {{ $transaction->final_price }}
                </div>
                <div class="total">
                     الاجمالي المستحق : {{ $transaction->final_price }}
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection

@section('script')
    <script>
    
        window.onload = function() {
            window.print();
        }
    </script>
@endsection
