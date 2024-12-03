<form action="{{ route('dashboard.sells.change-delivery-status-post', $transaction->id) }}" method="post">
    @csrf
    <div class="card-body">
        <div class="form-group">
            <label for="delivery_status">{{ trans('admin.Delivery-Status') }}</label>
            <select name="delivery_status" id="delivery_status" class="form-control">
                <option disabled selected>{{ trans('admin.Select-Delivery-Status') }}</option>
                <option value="ordered" @selected($transaction->delivery_status == 'ordered')>{{ trans('admin.Ordered') }}</option>
                <option value="shipped" @selected($transaction->delivery_status == 'shipped')>{{ trans('admin.Shipped') }}</option>
                <option value="delivered" @selected($transaction->delivery_status == 'delivered')>{{ trans('admin.Delivered') }}</option>
            </select>
            <br>
            <div class="form-group">
                <label for="delivery_status_note">{{ trans('admin.Delivery-Status-Note') }}</label>
                <textarea name="delivery_status_note" value="{{ $transaction->delivery_status_note }}" id="delivery_status_note" class="form-control" rows="3">{{$transaction->delivery_status_note}}</textarea>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ trans('admin.Save') }}</button>
    </div>
</form>
