<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-3">
                @include('components.form.input', [
                    'class' => 'form-control',
                    'name' => "contact",
                    'label' => trans('admin.contact'),
                    'value' => $sell->first()->Contact?->name,
                    'attribute' => 'required disabled',
                ])
            </div>
            <div class="col-lg-3">
                @include('components.form.input', [
                    'class' => 'form-control',
                    'name' => "contact",
                    'label' => trans('admin.type'),
                    'value' => $sell->first()->Contact?->type,
                    'attribute' => 'required disabled',
                ])
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <table id="example1" class="table table-bordered table-striped responsive">
            <thead>
            <tr>
                <th>#</th>
                <th>{{ trans('admin.method') }}</th>
                <th>{{ trans('admin.operation') }}</th>
                <th>{{ trans('admin.account') }}</th>
                <th>{{ trans('admin.amount') }}</th>
            </tr>
            </thead>
            <tbody>
                @foreach($sell as $transaction)
                    <tr>
                        <td>{{$transaction->id}}</td>
                        <td>{{$transaction->method}}</td>
                        <td>{{$transaction->operation}}</td>
                        <td>{{$transaction->account}}</td>
                        <td>{{$transaction->amount}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-9"></div>
            <div class="col-lg-3">
                <h4>{{ trans('admin.total') }} : {{ $sell->sum('amount') }}</h4>
            </div>
        </div>
    </div>
</div>