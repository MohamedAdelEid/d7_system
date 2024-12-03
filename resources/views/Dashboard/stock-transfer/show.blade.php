<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-3">
                @include('components.form.input', [
                    'class' => 'form-control',
                    'name' => "from_branch",
                    'label' => trans('admin.from_branch'),
                    'value' => $stock_transfer->Branch?->name,
                    'attribute' => 'required disabled',
                ])
            </div>
            <div class="col-lg-3">
                @include('components.form.input', [
                    'class' => 'form-control',
                    'name' => "to_branch",
                    'label' => trans('admin.to_branch'),
                    'value' => $stock_transfer->branchTo?->name,
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
                <th>{{trans('admin.unit')}}</th>
                <th>{{ trans('admin.price') }}</th>
            </tr>
            </thead>
            <tbody>
                @foreach ($stock_transfer->TransferLines as $line)
                    <tr>
                        <td>{{$line->Product?->name}}</td>
                        <td>{{$line->quantity}}</td>
                        <td>{{$line->Product?->MainUnit?->actual_name}}</td>
                        <td>{{$line->Product->unit_price}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>