<div class="card-body">
    <div class="row">
        <div class="col-lg-6">
            @include('components.form.input', [
                'type' => 'text',
                'class' => 'form-control',
                'attribute' => 'required',
                'name' => "name",
                'value' => isset($data) ? $data->name : old('name') ,
                'label' => trans('admin.name'),
            ])
        </div>

        <div class="col-lg-6">
            @include('components.form.select', [
                'collection' => $categories,
                'index' => 'id',
                'select' => isset($data) ? $data->parent_id : old('parent_id'),
                'name' => 'parent_id',
                'label' => trans('admin.Category'),
                'class' => 'form-control select2',
            ])
        </div>
        <div class="col-lg-6">
            <label for="cover">{{ trans('admin.cover') }} <p class="text-danger">مقاس الصورة (1354 * 201)</p></label>
            <input type="file" class="form-control" name="cover" id="cover" onchange="previewImage(event, 'coverPreview')">
            <div class="mt-2">
                <img id="coverPreview" src="{{ isset($data) && $data->getMedia('category_cover')->first() ? $data->getMedia('category_cover')->first()->getUrl() : '' }}" 
                     alt="Cover Preview" 
                     style="max-width: 100%; max-height: 200px; {{ isset($data) && $data->getMedia('category_cover')->first() ? '' : 'display: none;' }}">
            </div>
        </div>
    </div>
</div>


<script>
    function previewImage(event, previewId) {
        const input = event.target;
        const preview = document.getElementById(previewId);

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block'; // Show the image
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = ''; // Clear preview if no file selected
            preview.style.display = 'none';
        }
    }
</script>
