<div class="card-body">
    <div class="row">
        <div class="col-lg-4">
            @include('components.form.select', [
                'collection' => $branches,
                'index' => 'id',
                'select' => isset($stock_transfer) ? $stock_transfer->branch_id : old('from_branch_id'),
                'name' => 'from_branch_id',
                'label' => trans('admin.from_branch'),
                'class' => 'form-control select2 from_branch_id',
                'attribute' => 'required',
            ])
        </div>
        <div class="col-lg-4">
            @include('components.form.select', [
                'collection' => $branches,
                'index' => 'id',
                'select' => isset($stock_transfer) ? $stock_transfer->branch_to_id : old('to_branch_id'),
                'name' => 'to_branch_id',
                'label' => trans('admin.to_branch'),
                'class' => 'form-control select2 to_branch_id',
                'attribute' => 'required',
            ])
        </div>
        <div class="col-lg-4">
            @include('components.form.select', [
                'collection' => $products,
                'index' => 'id',
                'select' => old('product_id'),
                'name' => 'product_id',
                'label' => trans('admin.product'),
                'class' => 'form-control select2 products product_transfer_add',
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
                        <th>{{ trans('admin.transfer quantity') }}</th>
                        <th>{{ trans('admin.action') }}</th>
                    </tr>
                </thead>
                <tbody class="stock_transfer_table">
                    @foreach($stock_transfer->TransferLines as $line)
                        @php
                            $availableQuantity = $line->product->productBranchDetails()
                                ->where('branch_id', $stock_transfer->branch_id)
                                ->first()->qty_available ?? 0;
                        @endphp
                        <input type="hidden" name="products[{{ $line->product_id }}][id]" value="{{ $line->product_id }}">
                        <tr>
                            <td>{{ $line->product->name }}</td>
                            <td>{{ $line->Unit->actual_name }}</td>
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
