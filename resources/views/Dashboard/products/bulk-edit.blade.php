@extends('layouts.admin')

@section('title', trans('admin.bulk_edit_products'))

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-primary">{{ trans('admin.bulk_edit_products') }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('dashboard.home')}}" class="text-info">{{ trans('admin.Home') }}</a> / <span class="text-secondary">{{ trans('admin.bulk_edit_products') }}</span></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card bg-light">
              
                <!-- /.card-header -->
                <form method="POST" action="{{ route('dashboard.products.bulkUpdate') }}" enctype="multipart/form-data">
                    @csrf
                        @include('Dashboard.products.bulk-edit-form')
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">{{ trans('admin.Save') }}</button>
                    </div>
                </form>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4'
        });
    });
</script>
@endpush


