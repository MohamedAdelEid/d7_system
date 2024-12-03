<div class="card-body">
    <div class="row">
        <div class="col-lg-4">
            @include('components.form.select', [
                'collection' => $branches,
                'index' => 'id',
                'select' => isset($data) ? $data->branch_id : old('branch_id'),
                'name' => 'branch_id',
                'label' => trans('admin.branch'),
                'class' => 'form-control select2 branch_id',
                'attribute' => 'required',
            ])
        </div>

        <div class="col-lg-4">
            @include('components.form.select', [
                'collection' => $contacts,
                'index' => 'id',
                'select' => isset($data) ? $data->contact_id : old('contact_id'),
                'name' => 'contact_id',
                'label' => trans('admin.contact'),
                'class' => 'form-control select2',
                'attribute' => 'required',
            ])
        </div>

        <div class="col-lg-4">
            @include('components.form.select', [
                'collection' => $sells,
                'index' => 'id',
                'select' => isset($data) ? $data->sell_id : old('sell_id'),
                'name' => 'sell_id',
                'label' => trans('admin.sell transaction'),
                'class' => 'form-control select2',
                'attribute' => 'required',
            ])
        </div>

        <div class="col-lg-12">
            <div>
                @include('components.form.select', [
                    'collection' => $products,
                    'index' => 'id',
                    'select' => isset($data) ? $data->product_id : old('product_id'),
                    'name' => 'product_id',
                    'label' => trans('admin.product'),
                    'class' => 'form-control select2 products product_sell_add',
                    'id' => 'products',
                ])
            </div>
        </div>

        <div class="col-lg-12">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>{{ trans('admin.name') }}</th>
                    <th style="min-width: 120px;">{{ trans('admin.unit') }}</th>
                    <th>{{ trans('admin.quantity') }}</th>
                    <th>{{ trans('admin.available quantity') }}</th>
                    <th>{{ trans('admin.action') }}</th>
                </tr>
                </thead>
                <tbody class="sell_table">
                    @isset($data)
                        @foreach ($data->TransactionSellLines as $line)
                            @php
                                $product_row = $dataService->product_row($line->Product, $data->branch_id, $line);
                            @endphp
                            @include('Dashboard.sells.parts.product_row', [
                                'product_row'   => $product_row,
                            ])
                        @endforeach
                    @endisset
                </tbody>
            </table>
        </div>
        {{-- <div class="col-lg-12">
            <h4>{{ trans('admin.total') }} : <span class="final_total">0</span></h4>
        </div> --}}
    </div>
</div>
