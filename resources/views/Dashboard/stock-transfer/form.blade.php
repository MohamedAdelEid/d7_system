<div class="card-body">
    <div class="row">
        <div class="col-lg-4">
            @include('components.form.select', [
                'collection' => $branches,
                'index' => 'id',
                'select' => isset($data) ? $data->from_branch_id : old('from_branch_id'),
                'name' => 'from_branch_id',
                'label' => trans('admin.from_branch'),
                'class' => 'form-control select2 from_branch_id branch-select',
                'attribute' => 'required',
            ])
        </div>
        <div class="col-lg-4">
            @include('components.form.select', [
                'collection' => $branches,
                'index' => 'id',
                'select' => isset($data) ? $data->to_branch_id : old('to_branch_id'),
                'name' => 'to_branch_id',
                'label' => trans('admin.to_branch'),
                'class' => 'form-control select2 to_branch_id branch-select',
                'attribute' => 'required',
            ])
        </div>
        <div class="col-lg-4">
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
        <div class="col-lg-4">
            <label>{{trans('admin.status')}}</label>
            <select class="form-control select2" id="contactType" name="status">
                <option value="">{{ trans('admin.Select') }}</option>
                <option value="pending">{{ trans('admin.pending') }}</option>
                <option value="final">{{ trans('admin.final') }}</option>
            </select>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-lg-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ trans('admin.name') }}</th>
                        <th>{{ trans('admin.unit') }}</th>
                        <th>{{ trans('admin.available quantity') }}</th>
                        <th>{{ trans('admin.quantity') }}</th>
                        <th>{{ trans('admin.action') }}</th>
                    </tr>
                </thead>
                <tbody class="stock_transfer_table">
                </tbody>
            </table>
        </div>
    </div>
</div>