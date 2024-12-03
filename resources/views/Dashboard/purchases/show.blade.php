@php
    $deliveryStatuses = [
        'ordered' => trans('admin.Ordered'),
        'shipped' => trans('admin.Shipped'),
        'delivered' => trans('admin.Delivered')
    ];
    $paymentStatuses = [
        'due' => trans('admin.Due'),
        'final' => trans('admin.Final'),
        'partial' => trans('admin.Partial')
    ];
@endphp
<div class="row">
    {{-- Original Purchase Details --}}
    <div class="col-lg-12">
        <div class="row">   
            <div class="col-lg-4">
                @include('components.form.input', [
                    'class' => 'form-control',
                    'name' => "contact",
                    'label' => trans('admin.contact'),
                    'value' => $purchase->Contact?->name,
                    'attribute' => 'required disabled',
                ])
            </div>
            <div class="col-lg-4">
                @include('components.form.input', [
                    'class' => 'form-control',
                    'name' => "branch",
                    'label' => trans('admin.branch'),
                    'value' => $purchase->Branch?->name,
                    'attribute' => 'required disabled',
                ])
            </div>
            <div class="col-lg-4">
                @include('components.form.input', [
                    'class' => 'form-control',
                    'name' => "ref_no",
                    'label' => trans('admin.ref_no'),
                    'value' => $purchase->ref_no,
                    'attribute' => 'required disabled',
                ])
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="row">   
            <div class="col-lg-4">
                @include('components.form.input', [
                    'class' => 'form-control',
                    'name' => "transaction_date",
                    'label' => trans('admin.Created at'),
                    'value' => Carbon\Carbon::parse($purchase->transaction_date)->format('d-m-Y h:i A'),
                    'attribute' => 'required disabled',
                ])
            </div>
            <div class="col-lg-4">
                @include('components.form.input', [
                    'class' => 'form-control text-color-primary',
                    'name' => "delivery_status",
                    'label' => trans('admin.Delivery-Status'),
                    'value' => $deliveryStatuses[$purchase->delivery_status] ?? '',
                    'attribute' => 'required disabled',
                ])
            </div>
            <div class="col-lg-4">
                @include('components.form.input', [
                    'class' => 'form-control text-color-primary',
                    'name' => "payment_status",
                    'label' => trans('admin.payment_status'),
                    'value' => $paymentStatuses[$purchase->payment_status] ?? '',
                    'attribute' => 'required disabled',
                ])
            </div>
        </div>
    </div>

    {{-- Purchase Items Table --}}
    <div class="col-lg-12 mt-4">
        <h4>{{ trans('admin.purchase_items') }}</h4>
        <table id="example1" class="table table-bordered table-striped responsive">
            <thead>
            <tr>
                <th>#</th>
                <th>{{ trans('admin.product') }}</th>
                <th>{{ trans('admin.quantity') }}</th>
                <th>{{ trans('admin.unit') }}</th>
                <th>{{ trans('admin.unit_price') }}</th>
                <th>{{ trans('admin.total') }}</th>
                <th>{{ trans('admin.returned') }}</th>
            </tr>
            </thead>
            <tbody>
                @foreach ($purchase->TransactionPurchaseLines as $line)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$line->Product?->name}}</td>
                        <td>{{$line->quantity}}</td>
                        <td>{{$line->Unit?->actual_name}}</td>
                        <td>{{$line->unit_price}}</td>
                        <td>{{$line->quantity * $line->unit_price}}</td>
                        <td>{{$line->return_quantity}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- Purchase Update History --}}
    @if($purchase->purchaseUpdateHistories->count() > 0)
    <div class="col-lg-12 mt-4">
        <h4>{{ trans('admin.update_history') }}</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ trans('admin.date') }}</th>
                        <th>{{ trans('admin.old_qty') }}</th>
                        <th>{{ trans('admin.new_qty') }}</th>
                        <th>{{ trans('admin.old_total') }}</th>
                        <th>{{ trans('admin.new_total') }}</th>
                        <th>{{ trans('admin.updated_by') }}</th>
                        <th>{{ trans('admin.changes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->purchaseUpdateHistories->sortByDesc('created_at') as $history)
                    <tr>
                        <td>{{ Carbon\Carbon::parse($history->created_at)->format('d-m-Y h:i A') }}</td>
                        <td>{{ $history->old_final_price }}</td>
                        <td>{{ $history->new_final_price }}</td>
                        <td>{{ $history->old_total }}</td>
                        <td>{{ $history->new_total }}</td>
                        <td>{{ $history->updatedByUser->name }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#changesModal{{ $history->id }}">
                                {{ trans('admin.view_changes') }}
                            </button>
                            
                            <!-- Changes Modal -->
                            <div class="modal fade" id="changesModal{{ $history->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ trans('admin.changes_details') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <pre>{{ $history->changes_summary }}</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    {{-- Purchase History Section --}}
    @if($purchase->ReturnTransactions->count() > 0)
    <div class="col-lg-12 mt-4">
        <h4>{{ trans('admin.ref_history') }}</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ trans('admin.date') }}</th>
                        <th>{{ trans('admin.type') }}</th>
                        <th>{{ trans('admin.ref_no') }}</th>
                        <th>{{ trans('admin.amount') }}</th>
                        <th>{{ trans('admin.details') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->ReturnTransactions->sortBy('created_at') as $history)
                    <tr>
                        <td>{{ Carbon\Carbon::parse($history->created_at)->format('d-m-Y h:i A') }}</td>
                        <td>
                            <span class="badge {{ $history->type == 'purchase_return' ? 'bg-danger' : 'bg-info' }}">
                                {{ trans('admin.' . ucfirst($history->type)) }}
                            </span>
                        </td>
                        <td>{{ $history->ref_no }}</td>
                        <td>{{ $history->final_price }}</td>
                        <td>
                            <a href="{{ route('dashboard.purchases.purchase-return.index') }}" class="btn btn-sm btn-primary">
                                {{ trans('admin.view_details') }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

  

    {{-- Totals Section --}}
    <div class="col-lg-12 mt-4">
        <div class="row">
            <div class="col-lg-9"></div>
            <div class="col-lg-3">
                <h4>{{ trans('admin.total') }} : {{$purchase->total}}</h4>
                <h4>{{ trans('admin.discount') }} : {{$purchase->discount_value}}</h4>
                @if($purchase->ReturnTransactions->count() > 0)
                <h4>{{ trans('admin.net_amount') }} : {{$purchase->final_price + $purchase->ReturnTransactions->sum('final_price')}}</h4>
                <h4>{{ trans('admin.total_returned') }} : {{$purchase->ReturnTransactions->sum('final_price')}}</h4>
                @endif
                <h4>{{ trans('admin.final_price') }} : {{$purchase->final_price}}</h4>
            </div>
        </div>
    </div>
</div>