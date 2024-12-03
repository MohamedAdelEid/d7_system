<div class="card-body">
    <div class="row">
        <div class="col-lg-12">
            @include('components.form.input', [
                'class' => 'form-control',
                'name' => "governorate_name_ar",
                'label' => trans('admin.government'),
                'value' => isset($governorate) ? $governorate->governorate_name_ar : old('governorate_name_ar') ,
                'attribute' => 'required',
            ])
            @include('components.form.input', [
                'class' => 'form-control',
                'name' => "governorate_name_en",
                'label' => trans('admin.government').' en',
                'value' => isset($governorate) ? $governorate->government_en : old('government_en') ,
                'attribute' => 'required',
            ])
        </div>
   



    </div>
</div>

