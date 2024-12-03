{{-- @php
    $data = [];
    if (isset($data)) {
        $sub_unit_ids = $data->sub_unit_ids;
    }
@endphp --}}
<x-form.multiple-select class="form-control select2" id="" display="actual_name"  attribute="required"
:collection="$units" :selectArr="[]" index="id"
name="sub_unit_ids[]" label="{{ trans('admin.sub units') }}" />

