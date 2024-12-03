<div class="card-body">
    <div class="row">
        <div class="col-lg-6">
            @include('components.form.input', [
                'class' => 'form-control',
                'name' => 'title',
                'label' => trans('admin.title'),
                'value' => isset($data) ? $data->title : old('title'),
            ])
        </div>
        <div class="col-lg-6">
            @include('components.form.input', [
                'class' => 'form-control',
                'name' => 'sub_title',
                'label' => trans('admin.sub_title'),
                'value' => isset($data) ? $data->sub_title : old('sub_title'),
            ])
        </div>

    </div>

    <div class="row mt-3">
        <div class="col-lg-12">
            <label for="exampleInputFile">{{ trans('admin.image') }} <p class="text-danger">مقاس الصورة (1354 * 580)</p></label>
            <div class="custom-file">
                <input type="file" name="slider" class="custom-file-input" id="exampleInputFile" accept="image/*" onchange="previewSliderImage(event)">
                <label class="custom-file-label" for="exampleInputFile">image</label>
            </div>
            
            @if (isset($data) && method_exists($data, 'getMedia') && $data->getMedia('sliders')->first())
                <img src="{{  $data->getMedia('sliders')->first()->getUrl() }}" id="current-slider" style="width: 150px; height: auto; padding: 25px;">
            @endif
    
            <div id="new-slider-preview" style="display: none;">
                <img id="preview-slider-image" src="" style="width: 150px; height: auto; padding: 25px;">
            </div>
    
        </div>
    </div>
</div>
@section('script')


<script>
    // JavaScript function to preview the slider image when chosen
    function previewSliderImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('preview-slider-image');
            output.src = reader.result;
            document.getElementById('new-slider-preview').style.display = 'block';
            document.getElementById('current-slider').style.display = 'none'; // Hide the old slider image
            document.querySelector('.custom-file-label').textContent = event.target.files[0].name; // Update the label with the image name
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection