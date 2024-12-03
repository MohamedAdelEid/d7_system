@extends('layouts.frontend')

@section('title', trans('frontend.profile'))

@section('content')
    <ul class="breadcrumb">
        <li><a href="{{ route('index') }}">{{ trans('frontend.home') }}</a></li>
        <li class="active">{{ trans('frontend.profile') }} </li>
    </ul>
    
    <div class="row margin-bottom-40">
        <!-- BEGIN CONTENT -->
        <div class="sidebar col-md-3 col-sm-3">
            <ul class="list-group margin-bottom-25 sidebar-menu">
                <li class="list-group-item clearfix  {{request()->routeIs('profile') ? 'active':''}}"><a href="{{ route('profile') }}"><i class="fa fa-angle-right"></i>{{ __('frontend.profile') }}</a></li>
                <li class="list-group-item clearfix  {{request()->routeIs('profile.transaction') ? 'active':''}}"><a href="{{ route('profile.transaction') }}"><i class="fa fa-angle-right"></i>{{ __('frontend.my_orders') }}</a></li>
                <li class="list-group-item clearfix {{request()->routeIs('profile.edit') ? 'active':''}}"><a href="{{ route('profile.edit') }}"><i class="fa fa-angle-right"></i>{{ __('frontend.edit_profile') }}</a></li>
                <li class="list-group-item clearfix {{request()->routeIs('profile.password') ? 'active':''}}"><a href="{{ route('profile.password') }}"><i class="fa fa-angle-right"></i>{{ __('frontend.change_password') }}</a></li>
            </ul>
            
        </div>
        <div class="col-md-9 col-sm-7">
            <h1>{{ __('frontend.purchases_list') }}</h1>
           

            <!-- BEGIN CHECKOUT PAGE -->
            <div class="panel-group checkout-page accordion scrollable" id="checkout-page">

                <!-- BEGIN CHECKOUT -->
                <div id="checkout" class="panel panel-default">

                    <div id="checkout-content" class="panel-collapse collapse in p-5">
                        <div class="panel-body row">
                            <div class="table-wrapper-responsive text-center" style="padding:30px">
                                <table class="table table-bordered "  >
                                    <thead>
                                        <tr>
                                            <th class="checkout-description text-center">{{ __('frontend.invoice') }}</th>
                                            <th class="checkout-description text-center">{{ __('frontend.product_name') }}</th>
                                            <th class="checkout-quantity text-center">{{ __('frontend.quantity') }}</th>
                                            <th class="checkout-price text-center">{{ __('frontend.price') }}</th>
                                            <th class="checkout-total text-center">{{ __('frontend.total') }}</th>
                                        </tr>
                                    </thead>
                                    
                                    <tbody >
                                        @foreach ($transactions as $transaction)
                                            <tr >
                                                <td class="checkout-description text-center" style="text-align: center; vertical-align: middle;" rowspan="{{ $transaction->TransactionSellLines->count() }}" >
                                                    <h4>{{ $transaction->ref_no }}</h4>
                                                    <p class="small text-muted">{{ $transaction->created_at->format('Y-m-d') }}</p>
                                                    <p class="badge badge-success" style="  {{ $transaction->delivery_status == 'shipped' ? ' background-color: rgb(165, 121, 0)' : ($transaction->delivery_status == 'delivered' ? 'background-color: rgb(5, 100, 53)' : ' background-color: rgb(0, 141, 206)') }}">
                                                        @switch($transaction->delivery_status)
                                                            @case('shipped')
                                                                {{ __('frontend.shipped') }}
                                                                @break
                                                            @case('delivered')
                                                                {{ __('frontend.delivered') }}
                                                                @break
                                                            @case('ordered')
                                                                {{ __('frontend.ordered') }}
                                                                @break
                                                        @endswitch
                                                    </p>
                                                    
                                    
                                                    <!-- حالة payment_status -->
                                                    @if ($transaction->payment_status == 'final')
                                                        <p class="badge badge-success" style="background-color: rgb(5, 100, 53)" >{{ __('frontend.paid') }}</p>  <!-- إذا كانت الفاتورة مدفوعة -->
                                                    @elseif ($transaction->payment_status == 'due')
                                                        <p class="badge badge-warning" style="background-color: rgb(158, 0, 0)" >{{ __('frontend.due') }}</p>  <!-- إذا كانت الفاتورة مستحقة الدفع -->
                                                    @endif
                                                </td>
                                                @foreach ($transaction->TransactionSellLines as $index => $item)
                                                    @if ($index > 0) <tr> @endif
                                                        <td class="checkout-description text-center" style="text-align: center; vertical-align: middle;">
                                                            {{ $item->product->name }}
                                                        </td>
                                                        <td class="checkout-quantity text-center" style="text-align: center; vertical-align: middle;">{{ $item->quantity }}</td>
                                                        <td class="checkout-price text-center" style="text-align: center; vertical-align: middle;"><strong><span>{{ trans('frontend.currency') }} </span>{{ $item->unit_price }}</strong></td>
                                                        <td class="checkout-total text-center" style="text-align: center; vertical-align: middle;"><strong><span>{{ trans('frontend.currency') }} </span>{{ $item->total }}</strong></td>
                                                    </tr>
                                                @endforeach
                                        @endforeach
                                    </tbody>
                                    
                                </table>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END CHECKOUT -->


                <!-- END CONFIRM -->
            </div>
            <!-- END CHECKOUT PAGE -->
        </div>
        <!-- END CONTENT -->
    </div>
@endsection
