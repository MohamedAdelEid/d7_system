@include('components.form.select', [
    'collection' => $accounts,
    'index' => 'id',
    'select' => '',
    'name' => 'account_id',
    'label' => trans('admin.account'),
    'class' => 'form-control select2',
    'attribute' => 'required',
    'no_select' => true
])