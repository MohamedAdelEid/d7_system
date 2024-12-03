<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-3">
                @include('components.form.input', [
                    'class' => 'form-control',
                    'name' => "type",
                    'label' => trans('admin.type'),
                    'value' => $contact->type,
                    'attribute' => 'required disabled',
                ])
            </div>
            <div class="col-lg-3">
                @include('components.form.input', [
                    'class' => 'form-control',
                    'name' => "name",
                    'label' => trans('admin.name'),
                    'value' => $contact->name,
                    'attribute' => 'required disabled',
                ])
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <table id="example1" class="table table-bordered table-striped responsive">
            <thead>
            <tr>
                <th>{{ trans('admin.phone') }}</th>
                <th>{{ trans('admin.address') }}</th>
                <th>{{ trans('admin.balance') }}</th>
                <th>{{ trans('admin.opening_balance') }}</th>
                <th>{{ trans('admin.credit_limit') }}</th>
                <th>{{ trans('admin.sales_segment_id') }}</th>
                <th>{{ trans('admin.Created at') }}</th>
            </tr>
            </thead>
            <tbody>
                    <tr>
                        <td>{{$contact->phone}}</td>
                        <td>{{$contact->address ?? 'لا يوجد عنوان'}}</td>
                        <td>{{$contact->balance}}</td>
                        <td>{{$contact->opening_balance}}</td>
                        <td>{{$contact->credit_limit}}</td>
                        <td>{{$contact->salesSegment->name ?? 'لا يوجد شريحة مبيعات'}}</td>
                        <td>{{\Carbon\Carbon::parse($contact->created_at)->format('d-m-Y h:i')}}</td>
                    </tr>

            </tbody>
        </table>
    </div>

    <div class="col-lg-12">
        
    </div>
</div>