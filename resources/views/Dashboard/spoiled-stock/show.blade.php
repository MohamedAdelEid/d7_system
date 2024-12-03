<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-3">
                @include('components.form.input', [
                    'class' => 'form-control',
                    'name' => "branch",
                    'label' => trans('admin.branch'),
                    'value' => $spoiledStock->Branch?->name,
                    'attribute' => 'required disabled',
                ])
            </div>
            <div class="col-lg-3">
                @include('components.form.input', [
                    'class' => 'form-control',
                    'name' => "transaction_date",
                    'label' => trans('admin.transaction_date'),
                    'value' => \Carbon\Carbon::parse($spoiledStock->transaction_date)->format('d-m-Y h:i'),
                    'attribute' => 'required disabled',
                ])
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <table id="example1" class="table table-bordered table-striped responsive">
            <thead>
            <tr>
                <th>{{ trans('admin.product') }}</th>
                <th>{{ trans('admin.quantity') }}</th>
                <th>{{ trans('admin.status') }}</th>
                <th>{{ trans('admin.Created by') }}</th>
            </tr>
            </thead>
            <tbody>
                @foreach ($spoiledStock->SpoiledLines as $spoiledStockDetail)
                    <tr>
                        <td>{{$spoiledStockDetail->Product?->name}}</td>
                        <td>{{$spoiledStockDetail->quantity}}</td>
                        <td>{{$spoiledStock->status == 'pending' ? 'معلق' : 'منتهي'}}</td>
                        <td>{{$spoiledStock->CreatedBy?->name}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-lg-12">
        
    </div>
</div>