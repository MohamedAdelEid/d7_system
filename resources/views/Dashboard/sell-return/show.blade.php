@php
 $deliveryStatuses = [
        'delivered' => trans('admin.Delivered'),
        'shipped' => trans('admin.Shipped'),
        'ordered' => trans('admin.Ordered')
    ];
    $paymentStatuses = [
        'due' => trans('admin.Due'),
        'final' => trans('admin.Final'),
        'partial' => trans('admin.Partial')
    ];
@endphp
<div class="row">
  
    <div class="col-lg-12">
        <div class="row">   
            <div class="col-lg-4">
                @include('components.form.input', [
                    'class' => 'form-control',
                    'name' => "contact",
                    'label' => trans('admin.contact'),
                    'value' => $transaction->Contact?->name,
                    'attribute' => 'required disabled',
                ])
            </div>
            <div class="col-lg-4">
                @include('components.form.input', [
                    'class' => 'form-control',
                    'name' => "branch",
                    'label' => trans('admin.branch'),
                    'value' => $transaction->Branch?->name,
                    'attribute' => 'required disabled',
                ])
            </div>
            <div class="col-lg-4">
                @include('components.form.input', [
                    'class' => 'form-control',
                    'name' => "ref_no",
                    'label' => trans('admin.ref_no'),
                    'value' => $transaction->ref_no,
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
                    'value' => Carbon\Carbon::parse($transaction->transaction_date)->format('d-m-Y h:i A'),
                    'attribute' => 'required disabled',
                ])
            </div>
            <div class="col-lg-4">
                @include('components.form.input', [
                    'class' => 'form-control text-color-primary',
                    'name' => "delivery_status",
                    'label' => trans('admin.Delivery-Status'),
                    'value' => $deliveryStatuses[$transaction->delivery_status] ?? '',
                    'attribute' => 'required disabled',
                ])
            </div>
            <div class="col-lg-4">
                @include('components.form.input', [
                    'class' => 'form-control text-color-primary',
                    'name' => "payment_status",
                    'label' => trans('admin.payment_status'),
                    'value' => $paymentStatuses[$transaction->payment_status] ?? '',
                    'attribute' => 'required disabled',
                ])
            </div>
        </div>
    </div>


    <div class="col-lg-12">
        <table id="example1" class="table table-bordered table-striped responsive">
            <thead>
            <tr>
                <th>#</th>
                <th>{{ trans('admin.name') }}</th>
                <th>{{ trans('admin.return quantity') }}</th>
                <th>{{ trans('admin.unit_price') }}</th>
                <th>{{ trans('admin.total') }}</th>
            </tr>
            </thead>
            <tbody>
                @foreach ($transaction->TransactionPurchaseLines as $line)
                    <tr>
                        <td>{{$line->id}}</td>
                        <td>{{$line->Product?->name}}</td>
                        <td>{{$line->quantity}} {{$line->Unit?->actual_name}}</td>
                        <td>{{$line->unit_price}}</td>
                        <td>{{$line->unit_price * $line->quantity}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-9"></div>
            <div class="col-lg-3">
                <h4>{{ trans('admin.total') }} : {{$transaction->total}}</h4>
                <h4>{{ trans('admin.final_price') }} : {{$transaction->final_price}}</h4>
            </div>
        </div>
    </div>
</div>