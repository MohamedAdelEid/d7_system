@extends('layouts.admin')

@section('title', trans('admin.import-contacts'))

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('admin.import-contacts') }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">{{ trans('admin.Home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.contacts.index') }}">{{ trans('admin.contacts') }}</a></li>
                        <li class="breadcrumb-item active">{{ trans('admin.import') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{ trans('admin.import-contacts') }}</h3>
                        </div>
                        <form action="{{ route('dashboard.contacts.import.contacts') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="excel">{{ trans('admin.select-file') }}</label>
                                    <input type="file" name="excel" class="form-control" required>
                                    <small class="text-muted">{{ trans('admin.import-contacts-note') }}</small>
                                    <ul>
                                        <li class="text-danger">{{ trans('admin.code-note') }} - {{ trans('admin.optional') }}</li>
                                        <li class="text-danger">{{ trans('admin.name-note') }} - <span class="text-muted">{{ trans('admin.required*') }}</span></li>
                                        <li class="text-danger">{{ trans('admin.type-note') }} - <span class="text-muted">{{ trans('admin.required*') }}</span></li>
                                        <li class="text-danger">{{ trans('admin.phone-note') }} - {{ trans('admin.optional') }}</li>
                                        <li class="text-danger">{{ trans('admin.address-note') }} - {{ trans('admin.optional') }}</li>
                                        <li class="text-danger">{{ trans('admin.credit-limit-note-import') }} - {{ trans('admin.optional') }}</li>
                                        <li class="text-danger">{{ trans('admin.government-note') }} - {{ trans('admin.optional') }}</li>
                                        <li class="text-danger">{{ trans('admin.city-note') }} - {{ trans('admin.optional') }}</li>
                                        <li class="text-danger">{{ trans('admin.opening-balance-note') }} -  <span class="text-muted">{{ trans('admin.required*') }}</span></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ trans('admin.import') }}</button>
                                <a href="{{ route('dashboard.contacts.index') }}" class="btn btn-danger">{{ trans('admin.cancel') }}</a>
                                <a href="{{ asset('files/contacts/template.xlsx') }}" class="btn btn-success float-right" download>
                                    {{ trans('admin.download-template') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('styles')
<style>
    .alert-info ul {
        padding-right: 20px;
    }
    .alert-info ul li {
        list-style-type: none;
        position: relative;
    }
    .alert-info ul li:before {
        content: "•";
        position: absolute;
        right: -20px;
        color: #dc3545;
    }
</style>
@endpush