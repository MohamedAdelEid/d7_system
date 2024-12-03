<div class="card-body">
    <div class="row">
        <div class="col-lg-4">
            @include('components.form.select', [
                'collection' => $branches,
                'index' => 'id',
                'select' => isset($purchase) ? $purchase->branch_id : old('branch_id'),
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
                'select' => isset($purchase) ? $purchase->contact_id : old('contact_id'),
                'name' => 'supplier_id',
                'label' => trans('admin.supplier'),
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
                    'class' => 'form-control select2 products product_purchase_add',
                    'id' => 'products',
                ])
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ trans('admin.name') }}</th>
                        <th>{{ trans('admin.unit') }}</th>
                        <th>{{ trans('admin.quantity') }}</th>
                        <th>{{ trans('admin.available quantity') }}</th>
                        <th>{{ trans('admin.unit_price') }}</th>
                        <th>{{ trans('admin.total') }}</th>
                        <th>{{ trans('admin.action') }}</th>
                    </tr>
                </thead>
                <tbody class="purchase_table">
                    @if(isset($purchase_lines) && is_array($purchase_lines))
                        @foreach($purchase_lines as $product_row)
                            @include('Dashboard.purchases.parts.edit_product_raw', ['product_row' => $product_row])
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>


