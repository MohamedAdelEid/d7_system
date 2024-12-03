<form action="{{ route('dashboard.contacts.pay-popup-post', $contact->id) }}" method="post" class="card card-sm">
    @csrf
    <div class="card-body">
        @include('components.form.input', [
            'name' => 'amount',
            'label' => trans('admin.amount'),
            'class' => 'form-control',
            'value' =>  $total
        ])
        @include('components.form.select', [
            'name' => 'account_id',
            'label' => trans('admin.account'),
            'class' => 'form-control',
            'collection' => $accounts,
            'index' => 'id',
            'select' => $contact->id,
        ])
      
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ trans('admin.Save') }}</button>
    </div>
</form>
