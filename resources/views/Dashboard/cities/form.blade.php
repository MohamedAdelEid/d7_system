<div class="card-body">
    <div class="row">
        <div class="col-lg-6">
            @include('components.form.input', [
                'class' => 'form-control',
                'name' => "city_name_ar",
                'label' => trans('admin.city'),
                'value' => isset($city) ? $city->city_name_ar : old('city_name_ar') ,
                'attribute' => 'required',
            ])
        </div>
   
        <div class="col-lg-6">
            @include('components.form.input', [
                'class' => 'form-control',
                'name' => "city_name_en",
                'label' => trans('admin.city').' en',
                'value' => isset($city) ? $city->city_name_en : old('city_name_en') ,
                'attribute' => 'required',
            ])
        </div>
   

 
        <div class="col-lg-6">
            @include('components.form.select', [
                'collection' => $governorates,
                'index' => 'id',
                'id' => 'governorate_id',
                'select' => isset($city) ? $city->governorate_id : old('governorate_id'),
                'name' => 'governorate_id',
                'display' => app()->getLocale() == "ar" ? "governorate_name_ar" : "governorate_name_en",
                'label' => trans('admin.government'),
                'class' => 'form-control select2',
            ])
        </div>


    </div>
</div>

