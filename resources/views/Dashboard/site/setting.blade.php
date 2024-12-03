@extends('layouts.admin')

@section('title', trans('admin.Settings'))

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('admin.Settings') }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">{{ trans('admin.Home') }}</a> /
                            {{ trans('admin.Settings') }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="POST" action="" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-lg-5">
                                        @include('components.form.input', [
                                            'class' => 'form-control',
                                            'name' => 'email',
                                            'label' => trans('admin.email'),
                                            'value' => isset($data) ? $data->email : old('email'),
                                        ])
                                    </div>
                                    <div class="col-lg-5">
                                        @include('components.form.input', [
                                            'class' => 'form-control',
                                            'name' => 'phone',
                                            'label' => trans('admin.phone'),
                                            'value' => isset($data) ? $data->phone : old('phone'),
                                        ])
                                    </div>
                                    <div class="col-lg-2">
                                        @include('components.form.input', [
                                            'class' => 'form-control',
                                            'type' => 'number',
                                            'name' => 'tax',
                                            'label' => trans('admin.tax'),
                                            'value' => isset($data) ? $data->tax : old('tax'),
                                        ])
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label for="address">{{ trans('admin.address') }}</label>
                                        <textarea name="address" class="form-control" rows="5">{{ isset($data) ? $data->address : old('address') }}</textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        @include('components.form.input', [
                                            'class' => 'form-control',
                                            'name' => 'facebook',
                                            'label' => trans('admin.facebook'),
                                            'value' => isset($data) ? $data->facebook : old('facebook'),
                                        ])
                                    </div>
                                    <div class="col-lg-6">
                                        @include('components.form.input', [
                                            'class' => 'form-control',
                                            'name' => 'linkedin',
                                            'label' => trans('admin.linkedin'),
                                            'value' => isset($data) ? $data->linkedin : old('linkedin'),
                                        ])
                                    </div>
                                    <div class="col-lg-6">
                                        @include('components.form.input', [
                                            'class' => 'form-control',
                                            'name' => 'instagram',
                                            'label' => trans('admin.instagram'),
                                            'value' => isset($data) ? $data->instagram : old('instagram'),
                                        ])
                                    </div>
                                    <div class="col-lg-6">
                                        @include('components.form.input', [
                                            'class' => 'form-control',
                                            'name' => 'twitter',
                                            'label' => trans('admin.twitter'),
                                            'value' => isset($data) ? $data->twitter : old('twitter'),
                                        ])
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label for="about_us">{{ trans('admin.about_us') }}</label>
                                        <textarea name="about_us" class="form-control" rows="5">{{ isset($data) ? $data->about_us : old('about_us') }}</textarea>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-lg-12">
                                        <label for="exampleInputFile">{{ trans('admin.logo') }} <p class="text-danger">مقاس الصورة (128 * 32)</p></label>
                                        <div class="custom-file">
                                            <input type="file" name="logo" class="custom-file-input" id="exampleInputFile" accept="image/*" onchange="previewImage(event)">
                                            <label class="custom-file-label" for="exampleInputFile">Logo</label>
                                        </div>
                                        
                                        @if ($data && method_exists($data, 'getMedia') && $data->getMedia('logo')->first())
                                            <img src="{{  $data->getMedia('logo')->first()->getUrl() }}" id="current-logo" style="width: 150px; height: auto; padding: 25px;">
                                        @endif
                                
                                        <div id="new-logo-preview" style="display: none;">
                                            <img id="preview-image" src="" style="width: 150px; height: auto; padding: 25px;">
                                        </div>
                                
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ trans('admin.Save') }}</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                </div>
            </div><!-- /.container-fluid -->
    </section>
@endsection
@section('script')

<script>
    // JavaScript function to preview the image when chosen
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('preview-image');
            output.src = reader.result;
            document.getElementById('new-logo-preview').style.display = 'block';
            document.getElementById('current-logo').style.display = 'none'; // Hide the old logo
            document.querySelector('.custom-file-label').textContent = event.target.files[0].name; // Update the label with the image name
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection