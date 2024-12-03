<form action="{{ route('dashboard.accounts.changeStatusPost', $account->id) }}" method="post">
    @csrf
    <div class="card-body">
        <div class="form-group">
            <label for="status">{{ trans('admin.status') }}</label>
            <select name="status" id="status" class="form-control">
                <option disabled selected>اختر</option>
                <option value="1" {{ $account->is_active ? 'selected' : '' }}>{{ trans('admin.Active') }}</option>
                <option value="0" {{ $account->is_active ? '' : 'selected' }}>{{ trans('admin.Deactivate') }}</option>

            </select>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ trans('admin.Save') }}</button>
    </div>
</form>
