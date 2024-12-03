<div class="card-body">
    <div class="row">
        <div class="col-lg-4">
            <label>{{trans('admin.type')}}</label>
            <select class="form-control select2" id="contactType" name="type">
                <option value="">{{ trans('admin.Select') }}</option>
                <option value="customer" @if (isset($data) && $data->type == 'customer' || old('type') == 'customer') selected @endif>{{ trans('admin.customer') }}</option>
                <option value="supplier" @if (isset($data) && $data->type == 'supplier' || old('type') == 'supplier') selected @endif>{{ trans('admin.supplier') }}</option>
            </select>
            @error('type')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="col-lg-4">
            <x-form.input type="text" class="form-control" attribute="required"
                name="name" value="{{ isset($data) ? $data->name : old('name') }}"
                label="{{ trans('admin.name') }}"/>
            @error('name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="col-lg-4">
            <x-form.input type="text" class="form-control" attribute="required"
                name="phone" value="{{ isset($data) ? $data->phone : old('phone') }}"
                label="{{ trans('admin.phone') }}"/>
            @error('phone')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-lg-4" id="activity_type_id">
            @include('components.form.select', [
                'collection' => $activityTypes,
                'index' => 'id',
                'id' => 'activity_type_id',
                'select' => isset($data) ? $data->activity_type_id : old('activity_type_id'),
                'name' => 'activity_type_id',
                'display'=> 'name',
                'label' => trans('admin.activityType'),
                'class' => 'form-control select2',
            ])
        </div>
        @error('activity_type_id')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
 

        <div class="col-lg-4">
            @include('components.form.select', [
                'collection' => $governorates,
                'index' => 'id',
                'id' => 'governorate_id',
                'select' => isset($data) ? $data->governorate_id : old('governorate_id'),
                'name' => 'governorate_id',
                'display' => app()->getLocale() == "ar" ? "governorate_name_ar" : "governorate_name_en",
                'label' => trans('admin.government'),
                'class' => 'form-control select2',
            ])
            @error('governorate_id')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="col-lg-4" >
            @include('components.form.select', [
                'collection' => $cities,
                'index' => 'id',
                'id' => 'cities_dropdown',
                'select' => isset($data) ? $data->city_id : old('city_id'),
                'name' => 'city_id',
                'display' => app()->getLocale() == "ar" ? "city_name_ar" : "city_name_en",
                'label' => trans('admin.city'),
                'class' => 'form-control select2',
            ])
            @error('city_id')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-lg-4">
            <x-form.input type="number" class="form-control" 

                name="opening_balance"  value="{{ isset($data) ? $data->opening_balance : 0 }}"
                label="{{ trans('admin.opening_balance') }} <span class='text-danger'>{{ trans('admin.opening-balance-note') }}</span>"/>
            @error('opening_balance')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        @php
            $defaultCreditLimit = $settings->default_credit_limit;
        @endphp
        <div class="col-lg-4" id="creditLimitField">
            <x-form.input type="number" class="form-control" 
                name="credit_limit" value="{{ isset($data) ? $data->credit_limit : $defaultCreditLimit }}"
                label="{{ trans('admin.credit_limit')  }}. <span class='text-danger'>{{ trans('admin.credit-limit-note') }}</span>"/>
            @error('credit_limit')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="col-lg-4 mt-6" id="salesSegmentsField">
            <label>{{trans('admin.sales_segments')}}</label>
            <select name="sales_segment_id" class="form-control select2" style="width: 100%;">
                <option value="">{{ trans('admin.Select') }}</option>
                @foreach ($salesSegments as $segment)
                    <option value="{{ $segment->id }}" @if (isset($data) && $data->c == $segment->id || old('sales_segment_id') == $segment->id) selected @endif>{{ $segment->name }}</option>
                @endforeach
            </select>
            @error('sales_segment_id')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="col-lg-12">
            <x-form.input type="text" class="form-control" attribute="required"
                name="address" value="{{ isset($data) ? $data->address : old('address') }}"
                label="{{ trans('admin.address') }} <span class='text-danger'>{{ trans('admin.address-note') }}</span>"/> 
              
        </div>
        <div class="col-lg-4">
            <x-form.checkbox class="form-control" label="{{trans('admin.is_active')}}" tag="AddAsSubUnitAjax"
            value="1"  name="is_active"
            attribute="{{ isset($data) ? ($data->is_active != null) ? 'checked' : '' : '' }}"/>
            @error('is_active')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#governorate_id').change(function() {
            var governorate_id = $(this).val(); // Get the selected governorate ID
            var citiesDropdown = $('#cities_dropdown'); // Get the cities dropdown element
            let appLocale = "{{ app()->getLocale() }}";
            // Clear the cities dropdown
            citiesDropdown.empty();
            citiesDropdown.append('<option value="">{{ trans("admin.Select") }}</option>'); // Optional placeholder

            if (governorate_id) {
                // Make AJAX request to fetch cities
                $.ajax({
                    url: '{{ route("dashboard.branchs.getCitiesByGovernorate") }}',
                    method: 'GET',
                    data: {
                        governorate_id: governorate_id
                    },
                    success: function(data) {
                        console.log(data); 
                        $.each(data, function(index, city) {
                            citiesDropdown.append('<option value="' + city.id + '">' + (appLocale === "ar" ? city.city_name_ar : city.city_name_en) + '</option>');
                        });

                        citiesDropdown.select2(); // Reinitialize Select2
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr);
                        alert('An error occurred while fetching cities. Please try again.');
                    }
                });
            }
        });
    });
</script>