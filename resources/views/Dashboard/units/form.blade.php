<div class="card-body">
    <div class="row">
        <div class="col-lg-6">
            <x-form.input type="text" class="form-control" attribute="required"
                name="actual_name" value="{{ isset($data) ? $data->actual_name : old('actual_name') }}"
                label="{{ trans('admin.actual_name') }}"/>
        </div>

        <div class="col-lg-6">
            <x-form.input type="text" class="form-control" attribute="required"
                name="short_name" value="{{ isset($data) ? $data->short_name : old('short_name') }}"
                label="{{ trans('admin.short_name') }}"/>
        </div>

        <div class="col-lg-12">
            <x-form.checkbox class="form-control AddAsSubUnitAjax" label="{{trans('admin.Add as sub unit')}}" tag="AddAsSubUnitAjax"
            value="1"  name="is_sub_unit"
            attribute="{{ isset($data) ? ($data->base_unit_id != null) ? 'checked' : '' : '' }}"/>
        </div>

        <div class="col-lg-12">
            <div class="row addAsSubUnitDev" style="@if (!(isset($data) && $data->base_unit_id != null)) display: none @endif">
                <div class="col-lg-6">
                    <x-form.select class="form-control select2" id=""
                    :collection="$base_units" select="{{ isset($data) ? $data->base_unit_id : old('base_unit_id') }}" index="id"
                    name="base_unit_id" label="{{ trans('admin.base_unit') }}" display="actual_name" attribute="" />
                </div>
    
                <div class="col-lg-6">
                    <x-form.input type="number" class="form-control" attribute=""
                        name="base_unit_multiplier" value="{{ isset($data) ? $data->base_unit_multiplier : old('base_unit_multiplier') }}"
                        label="{{ trans('admin.base_unit_multiplier') }}"/>
                </div>
    
                <div class="col-lg-12">
                    <x-form.checkbox class="form-control" label="{{trans('admin.base_unit_is_largest')}}" tag="{{trans('admin.base_unit_is_largest')}}"
                    value="1"  name="base_unit_is_largest"
                    attribute="{{ isset($data) ? ($data->base_unit_is_largest != null) ? 'checked' : '' : '' }}"/>
                </div>
            </div>
        </div>

    </div>
</div>