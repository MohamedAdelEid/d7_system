<x-form.select class="form-control select2" id=""
:collection="$categories" select="{{ isset($data) ? $data->category_id : old('category_id') }}" index="id"
name="category_id" label="{{ trans('admin.category') }}"  />