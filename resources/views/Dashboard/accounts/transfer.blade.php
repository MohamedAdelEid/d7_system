@extends('layouts.admin')

@section('title', __('admin.Transfer'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('admin.Transfer') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('dashboard.accounts.transfer.post') }}">
                            @csrf
                            <input type="hidden" name="from_account" value="{{ $account->id }}">
                            <div class="form-group row">
                                <label for="from_account" class="col-md-4 col-form-label text-md-right">{{ __('admin.From Account') }}</label>

                                <div class="col-md-6">
                                    <select id="from_account" class="form-control @error('from_account') is-invalid @enderror" name="from_account" required>
                                        <option disabled selected value="{{ $account->id }}">{{ $account->name }} ({{ $account->number }})</option>
                                    </select>

                                    @error('from_account')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="to_account" class="col-md-4 col-form-label text-md-right">{{ __('admin.To Account') }}</label>

                                <div class="col-md-6">
                                    <select id="to_account" class="form-control @error('to_account') is-invalid @enderror" name="to_account" required>
                                        <option value="">{{ __('admin.Select Account') }}</option>
                                        @foreach(\App\Models\Account::all() as $acc)
                                            <option value="{{ $acc->id }}" {{ $acc->id == $account->id ? 'hidden' : '' }}>{{ $acc->name }} ({{ $acc->number }})</option>
                                        @endforeach
                                    </select>

                                    @error('to_account')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="amount" class="col-md-4 col-form-label text-md-right">{{ __('admin.amount') }}</label>

                                <div class="col-md-6">
                                    <input id="amount" type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" required autocomplete="amount" autofocus>

                                    @error('amount')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('admin.Transfer') }}
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

@push('scripts')
<script>
    $(document).ready(function() {
        $('#from_account, #to_account').select2();
    });
</script>
@endpush
