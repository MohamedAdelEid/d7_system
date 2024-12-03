<form method="post" action="{{ isset($expense) ? route('dashboard.expenses.update', $expense->id) : route('dashboard.expenses.store') }}">
    @csrf
 
    <div class="card-body">
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="expense_category_id">{{ trans('admin.select_expense_categories') }}</label>
                    <select name="expense_category_id" id="expense_category_id" class="form-control">
                        @foreach($expenseCategories as $category)
                            <option value="{{ $category->id }}" {{ isset($expense) && $expense->expense_category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="account_id">{{ trans('admin.Select Account') }}</label>
                    <select name="account_id" id="account_id" class="form-control">
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ isset($expense) && $expense->account_id == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="created_by">{{ trans('admin.Select Account') }}</label>
                    <select name="created_by" id="created_by" class="form-control">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ isset($expense) && $expense->created_by == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="branch_id">{{ trans('admin.Select Branch') }}</label>
                    <select name="branch_id" id="branch_id" class="form-control">
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ isset($expense) && $expense->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="amount">{{ trans('admin.amount') }}</label>
                    <input type="text" name="amount" id="amount" class="form-control" value="{{ isset($expense) ? $expense->amount : old('amount') }}" required>
                    @error('amount')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="form-group">
                    <label for="note">{{ trans('admin.note') }}</label>
                    <textarea name="note" id="note" class="form-control">{{ isset($expense) ? $expense->note : '' }}</textarea>
                </div>
            </div>
        </div>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ isset($expense) ? trans('admin.update') : trans('admin.Create') }}</button>
    </div>
</form>
