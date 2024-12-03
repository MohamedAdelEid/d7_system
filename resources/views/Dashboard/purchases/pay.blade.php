<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <form method="POST" style="width: 100%" action="{{ route('dashboard.purchases.payTransactionPurchas', $transaction->id) }}">
                @csrf

                <div class="form-group row">
                    <label for="amount" class="col-md-4 col-form-label text-md-right">{{ __('admin.amount') }}</label>

                    <div class="col-md-6">
                        <input id="amount" type="number" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount', $amountMustPay) }}" required autocomplete="amount" autofocus>

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
</div