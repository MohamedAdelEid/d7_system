<form action="{{ route('dashboard.stock-transfers.changeStatus', $transaction->id) }}" method="post">
    @csrf
    <div class="card-body">
        <div class="form-group">
            <label for="status">{{ trans('admin.status') }}</label>
            <select name="status" id="status" class="form-control">
                <option disabled selected>اختر</option>
                <option value="pending">{{ trans('admin.pending') }}</option>
                <option value="final">{{ trans('admin.final') }}</option>

            </select>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ trans('admin.Save') }}</button>
    </div>
</form>
