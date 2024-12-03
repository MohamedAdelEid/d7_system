<div class="card-body">
    <div class="row">
        <div class="col-lg-12">
            @include('components.form.input', [
                'class' => 'form-control',
                'name' => "name",
                'label' => trans('admin.Name'),
                'value' => isset($data) ? $data->name : old('name'),
                'attribute' => 'required',
            ])
        </div>
        <div class="col-lg-6">
            <label for="image">{{ trans('admin.image') }} <p class="text-danger">مقاس الصورة (165 * 100)</p></label>
            <input type="file" class="form-control" name="image" id="image" onchange="previewImage(event, 'imagePreview')">
            <div class="mt-2">
                <img id="imagePreview" src="{{ isset($data) && $data->getMedia('brands')->first() ? $data->getMedia('brands')->first()->getUrl() : '' }}" 
                     alt="Image Preview" 
                     style="max-width: 100%; max-height: 200px; {{ isset($data) && $data->getMedia('brands')->first() ? '' : 'display: none;' }}">
            </div>
        </div>
        <div class="col-lg-6">
            <label for="cover">{{ trans('admin.cover') }} <p class="text-danger">مقاس الصورة (1354 * 201)</p></label>
            <input type="file" class="form-control" name="cover" id="cover" onchange="previewImage(event, 'coverPreview')">
            <div class="mt-2">
                <img id="coverPreview" src="{{ isset($data) && $data->getMedia('brand_cover')->first() ? $data->getMedia('brand_cover')->first()->getUrl() : '' }}" 
                     alt="Cover Preview" 
                     style="max-width: 100%; max-height: 200px; {{ isset($data) && $data->getMedia('brand_cover')->first() ? '' : 'display: none;' }}">
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
