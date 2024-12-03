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
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group">
                <label for="discount_value">{{ trans('admin.discount') }}</label>
                <input type="number" class="form-control" id="discount_value" name="discount_value" value="{{ isset($sell) ? $sell->discount_value : old('discount_value') }}" placeholder="{{ trans('admin.discount') }}" required>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group">
                <label for="discount_type">{{ trans('admin.discount type') }}</label>
                <select class="form-control" id="discount_type" name="discount_type" required>
                    <option value="percentage" {{ isset($sell) && $sell->discount_type == 'percentage' ? 'selected' : '' }}>{{ trans('admin.percentage') }}</option>
                    <option value="fixed_price" {{ isset($sell) && $sell->discount_type == 'fixed_price' ? 'selected' : '' }}>{{ trans('admin.fixed amount') }}</option>
                </select>
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
                        <th>{{ trans('admin.purchase_price') }}</th>
                        <th>{{ trans('admin.total') }}</th>
                        <th>{{ trans('admin.action') }}</th>
                    </tr>
                </thead>
                <tbody class="purchase_table">
                </tbody>
            </table>
        </div>
    </div>
</div>
