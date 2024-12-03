<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <form method="POST" style="width: 100%" action="">
                @csrf

                <div class="form-group row">
                    <label for="amount" class="col-md-4 col-form-label text-md-right">{{ __('admin.amount') }}</label>

                    <div class="col-md-6">
                        <input id="amount" form="purchase" type="number" class="form-control @error('amount') is-invalid @enderror" name="amount" autocomplete="amount" autofocus>

                        @error('amount')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" form="purchase" value="multi_pay" name="sell_type" class="btn button-send btn-primary">
                            {{ __('admin.Pay') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div