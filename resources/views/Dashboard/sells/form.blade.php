@php
    $is_edit = false;
    if(isset($sell))
        $is_edit = true;

    $disabled = '';
    if($is_edit)
        $disabled= 'disabled';
  
@endphp
<div class="card-body">
    <div class="row">
        <div class="col-lg-3">
            @include('components.form.select', [
                'collection' => $branches,
                'index' => 'id',
                'select' => isset($sell) ? $sell->branch_id : Auth::user()->branch_id,
                'name' => 'branch_id',
                'label' => trans('admin.branch'),
                'class' => 'form-control select2 branch_id',
                'attribute' => 'required ' . $disabled,
            ])
        </div>

        <div class="col-lg-3">
            @include('components.form.select', [
                'collection' => $contacts,
                'index' => 'id',
                'select' => isset($sell) ? $sell->contact_id : $cash_contact?->id,
                'name' => 'contact_id',
                'label' => trans('admin.contact'),
                'class' => 'form-control select2 contact_id',
                'attribute' => 'required ' . $disabled,
            ])
        </div>

        <div class="col-lg-3">
            <button type="button" class="btn btn-info fire-popup"
                data-toggle="modal"
                data-target="#modal-default"
                data-url="{{route('dashboard.sells.AddBulckProductsPopUp')}}">{{ trans('admin.Add Bulck products') }}</button>
        </div>

        <div class="col-lg-12">
            <div>
                @include('components.form.select', [
                    'collection' => $products,
                    'index' => 'id',
                    'select' => isset($sell) ? $sell->product_id : old('product_id'),
                    'name' => 'product_id',
                    'label' => trans('admin.product'),
                    'class' => 'form-control select2 products product_sell_add',
                    'id' => 'products',
                ])
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group">
                <label for="discount_value">{{ trans('admin.discount') }}</label>
                <input type="number" class="form-control" id="discount_value" name="discount_value" value="{{ isset($sell) ? $sell->discount_value : old('discount_value') }}" placeholder="{{ trans('admin.discount') }}" required>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group">
                <label for="discount_type">{{ trans('admin.discount type') }}</label>
                <select class="form-control" id="discount_type" name="discount_type" required>
                    <option value="percentage" {{ isset($sell) && $sell->discount_type == 'percentage' ? 'selected' : '' }}>{{ trans('admin.percentage') }}</option>
                    <option value="fixed_price" {{ isset($sell) && $sell->discount_type == 'fixed_price' ? 'selected' : '' }}>{{ trans('admin.fixed amount') }}</option>
                </select>
            </div>
        </div>
        <div class="col-lg-12">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>{{ trans('admin.name') }}</th>
                    <th style="min-width: 120px;">{{ trans('admin.unit') }}</th>
                    <th>{{ trans('admin.quantity') }}</th>
                    <th>{{ trans('admin.available quantity') }}</th>
                    <th>{{ trans('admin.unit_price') }}</th>
                    <th>{{ trans('admin.total') }}</th>
                    <th>{{ trans('admin.action') }}</th>
                </tr>
                </thead>
                 
                <tbody class="sell_table">
                    @isset($sell)
                        @foreach ($sell->TransactionSellLines as $line)
                        
                            @php
                                $product_row = $SellService->product_row($line->Product, $sell->branch_id, $line);
                            @endphp
                            @include('Dashboard.sells.parts.product_row', [
                                'product_row'   => $product_row,
                            ])
                        @endforeach
                    @endisset
                </tbody>
            </table>
        </div>
        <div class="col-lg-12">
            <h4>{{ trans('admin.total') }} : <span class="final_total">{{isset($sell) ? $sell->final_price  : 0}}</span></h4>
        </div>
   
    </div>
</div>
