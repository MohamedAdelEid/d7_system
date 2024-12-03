<div class="card-body">
    <div class="row">
        <div class="col-lg-12">
            @include('components.form.input', [
                'class' => 'form-control',
                'name' => "name",
                'label' => trans('admin.activityType'),
                'value' => isset($activityType) ? $activityType->name : old('name') ,
                'attribute' => 'required',
            ])
        </div>
    </div>
</div>

