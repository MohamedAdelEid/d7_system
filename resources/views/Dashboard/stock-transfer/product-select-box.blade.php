@include('components.form.select', [
    'collection' => $products,
    'index' => 'id',
    'select' => isset($data) ? $data->product_id : old('product_id'),
    'name' => 'product_id',
    'label' => trans('admin.product'),
    'class' => 'form-control select2',
    'id' => 'products',
])