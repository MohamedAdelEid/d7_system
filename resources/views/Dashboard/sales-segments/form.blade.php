<div class="card-body">
    <div class="row">
        <div class="col-lg-6">
            @include('components.form.input', [
                'class' => 'form-control',
                'name' => "name",
                'label' => trans('admin.Name'),
                'value' => isset($salesSegment) ? $salesSegment->name : old('name') ,
                'attribute' => 'required',
            ])
        </div>
        <div class="col-lg-6">
            @include('components.form.input', [
                'class' => 'form-control',
                'name' => "description",
                'label' => trans('admin.Description'),
                'value' => isset($salesSegment) ? $salesSegment->description : old('description') ,
            ])
        </div>
    </div>
</div>