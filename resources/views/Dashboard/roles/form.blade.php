<div class="card-body">
    <div class="row">
        <div class="col-lg-6">
            @include('components.form.input', [
                'class' => 'form-control',
                'name' => 'name',
                'label' => trans('admin.Name'),
                'value' => isset($data) ? $data->name : old('name'),
                'attribute' => 'required',
            ])
        </div>

        <div class="col-lg-6">
            @include('components.form.input', [
                'class' => 'form-control',
                'name' => 'description',
                'label' => trans('admin.Description'),
                'value' => isset($data) ? $data->description : old('description'),
                'attribute' => 'required',
            ])
        </div>
    </div>

    <div class="row">

        <div class="col-lg-3">
            <div class="card card-primary">
                <div class="card-header">

                    <h3 class="card-title">{{ trans('admin.test') }}</h3>
                </div>

                <div class="card-body">


                    @include('components.form.checkbox', [
                        'class' => 'form-control',
                        'label' => trans('admin.select_all'),
                        'tag' => 'allcheckbox',
                        'value' => '',
                        'name' => '',
                        'attribute' => '',
                    ])
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        @foreach (config('global.roles') as $key => $values)
            <div class="col-lg-3">
                <div class="card card-primary">
                    <div class="card-header">

                        <h3 class="card-title">{{ trans('admin.' . $key) }}</h3>
                    </div>

                    <div class="card-body">

                        @foreach ($values as $value)
                            @include('components.form.checkbox', [
                                'class' => 'form-control',
                                'label' => trans('admin.' . $value),
                                'tag' => $value . '-' . $key,
                                'value' => $value . '-' . $key,
                                'name' => 'permissions[]',
                                'id' => 'checkbox',
                                'class' => 'checkbox',
                                'attribute' => isset($data)
                                    ? ($data->hasPermission($value . '-' . $key)
                                        ? 'checked'
                                        : '')
                                    : '',
                            ])
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    $('#allcheckbox').click(function() {
        var checkboxes = $('.checkbox');

        // If the #allcheckbox is checked, check all .checkbox elements
        if ($(this).prop('checked')) {
            checkboxes.prop('checked', true);
        } else {
            // If #allcheckbox is unchecked, uncheck all .checkbox elements
            checkboxes.prop('checked', false);
        }
        console.log(checkboxes);
    });
</script>
