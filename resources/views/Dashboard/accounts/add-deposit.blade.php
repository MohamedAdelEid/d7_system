@extends('layouts.admin')

@section('title', __('admin.Deposit'))

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ trans('admin.Deposit') }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ trans('admin.Dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.accounts.index') }}">{{ trans('admin.accounts') }}</a></li>
                            <li class="breadcrumb-item active">{{ trans('admin.Deposit') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{ trans('admin.Deposit') }}</h3>
                            </div>
                            <form action="{{ route('dashboard.accounts.add-deposit-post', $account->id) }}" method="post">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="amount">{{ trans('admin.amount') }}</label>
                                        <input type="number" step="0.01" name="amount" class="form-control" id="amount" placeholder="{{ trans('admin.amount') }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="balance">{{ trans('admin.balance') }}</label>
                                        <input type="text" class="form-control" id="balance" value="{{ $account->balance }}" readonly>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">{{ trans('admin.Deposit') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
