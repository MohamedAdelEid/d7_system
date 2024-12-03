@extends('layouts.admin')

@section('title', trans('admin.contacts'))

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0">{{ trans('admin.contacts') }}</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('dashboard.home')}}">{{ trans('admin.Home') }}</a> / <a href="{{route('dashboard.contacts.index')}}">{{ trans('admin.contacts') }}</a> / {{ trans('admin.Edit') }}</li>
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
                <h3 class="card-title">{{ trans('admin.Edit') }}</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form method="post" action="">
                    @csrf
                    @include('Dashboard.contacts.form')

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
    $(document).ready(function() {
    // Call toggleFields function initially to set correct visibility based on the current value
    toggleFields($('#contactType').val());

    // Event listener for type change
    $('#contactType').on('change', function() {
        let selectedType = $(this).val();
        toggleFields(selectedType);
    });

    // Function to toggle the visibility of fields
    function toggleFields(selectedType) {
        if (selectedType === 'customer') {
            $('#creditLimitField').show();
            $('#salesSegmentsField').show();
        } else if (selectedType === 'supplier') {
            $('#creditLimitField').hide();
            $('#salesSegmentsField').hide();
        } else {
            // Hide fields if neither customer nor supplier is selected
            $('#creditLimitField').hide();
            $('#salesSegmentsField').hide();
        }
    }
    });
</script>
@endsection

