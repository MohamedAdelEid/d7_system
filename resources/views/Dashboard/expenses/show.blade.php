<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-3">
                @include('components.form.input', [
                    'class' => 'form-control',
                    'name' => "account",
                    'label' => trans('admin.account'),
                    'value' => $expense->Account?->name,
                    'attribute' => 'required disabled',
                ])
            </div>
            <div class="col-lg-3">
                @include('components.form.input', [
                    'class' => 'form-control',
                    'name' => "branch",
                    'label' => trans('admin.branch'),
                    'value' => $expense->Branch?->name,
                    'attribute' => 'required disabled',
                ])
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <table id="example1" class="table table-bordered table-striped responsive">
            <thead>
            <tr>
                <th>{{ trans('admin.Expense Category') }}</th>
                <th>{{ trans('admin.amount') }}</th>
                <th>{{ trans('admin.note') }}</th>
                <th>{{ trans('admin.Created by') }}</th>
                <th>{{ trans('admin.Created at') }}</th>
            </tr>
            </thead>
            <tbody>
                    <tr>
                        <td>{{$expense->ExpenseCategory?->name}}</td>
                        <td>{{$expense->amount}}</td>
                        <td>{{$expense->note ?? 'لا يوجد ملاحظة'}}</td>
                        <td>{{$expense->CreatedBy?->name}}</td>
                        <td>{{\Carbon\Carbon::parse($expense->created_at)->format('d-m-Y h:i')}}</td>
                    </tr>

            </tbody>
        </table>
    </div>

    <div class="col-lg-12">
        
    </div>
</div>