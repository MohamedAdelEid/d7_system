<div class="card-body">
    <div class="row">
        <div class="col-lg-6">
            @include('components.form.input', [
                'class' => 'form-control',
                'name' => "name",
                'label' => trans('admin.Name'),
                'value' => isset($data) ? $data->name : old('name') ,
                'attribute' => 'required',
            ])
        </div>
    </div>
</div>