<div class="card-body">
    <div class="row">
        <div class="col-lg-4">
            @include('components.form.input', [
                'class' => 'form-control',
                'name' => "name",
                'label' => trans('admin.Name'),
                'value' => isset($data) ? $data->name : old('name') ,
                'attribute' => 'required',
            ])
        </div>

        <div class="col-lg-4">
            @include('components.form.select', [
                'collection' => $accounts,
                'index' => 'id',
                'select' => isset($data) ? $data->cash_account_id : old('cash_account_id'),
                'name' => 'cash_account_id',
                'label' => trans('admin.cash_account'),
                'class' => 'form-control select2',
            ])
        </div>

        <div class="col-lg-4">
            @include('components.form.select', [
                'collection' => $accounts,
                'index' => 'id',
                'select' => isset($data) ? $data->credit_account_id : old('credit_account_id'),
                'name' => 'credit_account_id',
                'label' => trans('admin.credit_account'),
                'class' => 'form-control select2',
            ])
        </div>
        <div class="col-lg-6">
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
        </div>

        <div class="col-lg-6">
            <label>{{ trans('admin.cities') }}</label>
            <select multiple id="cities_dropdown" class="form-control select2" name="city_ids[]">
                <option value="">{{ trans('admin.Select') }}</option>
                    @foreach ($cities as $city)
                    <option value="{{ $city->id }}" 
                        @if (in_array($city->id, $selectedCities)) selected @endif>
                        {{ app()->getLocale() == "ar" ? $city->city_name_ar : $city->city_name_en }}
                    </option>
                    @endforeach
            </select>
        
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