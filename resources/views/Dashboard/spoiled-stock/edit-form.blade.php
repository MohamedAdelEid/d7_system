<div class="card-body">
    <div class="row">
        <div class="col-lg-6">
            @include('components.form.select', [
                'collection' => $branches,
                'index' => 'id',
                'select' => isset($spoiledStock) ? $spoiledStock->branch_id : old('branch_id'),
                'name' => 'branch_id',
                'label' => trans('admin.branch'),
                'class' => 'form-control select2 branch_id',
                'attribute' => 'required',
            ])
        </div>
        <div class="col-lg-6">
            @include('components.form.select', [
                'collection' => $products,
                'index' => 'id',
                'select' => old('product_id'),
                'name' => 'product_id',
                'label' => trans('admin.product'),
                'class' => 'form-control select2 products product_spoiled_add',
                'id' => 'products',
            ])
        </div>
       
    </div>
    
    <div class="row mt-4">
        <div class="col-lg-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ trans('admin.name') }}</th>
                        <th>{{ trans('admin.unit') }}</th>
                        <th>{{ trans('admin.available quantity') }}</th>
                        <th>{{ trans('admin.spoiled quantity') }}</th>
                        <th>{{ trans('admin.action') }}</th>
                    </tr>
                </thead>
                <tbody class="spoiled_stock_table">
                    @foreach($spoiledStock->SpoiledLines as $line)
                        @php
                            $availableQuantity = $line->product->productBranchDetails()
                                ->where('branch_id', $spoiledStock->branch_id)
                                ->first()->qty_available ?? 0;
                        @endphp
                        <input type="hidden" name="products[{{ $line->product_id }}][id]" value="{{$line->product_id}}">
                        <tr>
                            <td>{{ $line->product->name }}</td>
                            <td>{{ $line->Unit?->actual_name }}<input type="hidden" value="{{$line->unit_id}}" name="products[{{ $line->product_id }}][unit_id]"></td>
                            <td>{{ $availableQuantity }}</td>
                            <td>
                                <input type="number" name="products[{{ $line->product_id }}][quantity]" 
                                        value="{{ $line->quantity }}" class="form-control" 
                                        min="1" max="{{ $availableQuantity + $line->quantity }}" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-line">{{ trans('admin.Delete') }}</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
