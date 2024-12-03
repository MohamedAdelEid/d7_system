@extends('layouts.admin')

@section('title', __('admin.Pay'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('admin.Pay') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('dashboard.contacts.pay', $contact->id) }}">
                            @csrf

                            <div class="form-group row">
                                <label for="amount" class="col-md-4 col-form-label text-md-right">{{ __('admin.amount') }}</label>

                                <div class="col-md-6">
                                    <input id="amount" type="number" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount', $total) }}" required autocomplete="amount" autofocus>

                                    @error('amount')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="account_id" class="col-md-4 col-form-label text-md-right">{{ __('admin.account') }}</label>

                                <div class="col-md-6">
                                    <select id="account_id" class="form-control @error('account_id') is-invalid @enderror" name="account_id" required>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                                        @endforeach
                                    </select>

                                    @error('account_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                          

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('admin.Pay') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
