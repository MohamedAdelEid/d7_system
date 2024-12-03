<tr class="product_row_{{$product_row['id']}}">
    <input type="hidden" name="products[{{$product_row['id']}}][id]" value="{{$product_row['id']}}">
    <input type="hidden" class="main_unit_price_{{$product_row['id']}}" name="products[{{$product_row['id']}}][main_unit_price]" value="{{$product_row['purchase_price']}}">
    <input type="hidden" class="main_available_quantity_{{$product_row['id']}}" name="products[{{$product_row['id']}}][main_available_quantity]" value="{{$product_row['available_quantity']}}">
    <input type="hidden" class="unit_multipler_{{$product_row['id']}}" name="products[{{$product_row['id']}}][unit_multipler]" value="1">
    <td>
        @include('components.form.input', [
            'type' => 'text',
            'class' => 'form-control',
            'attribute' => 'required readonly',
            'name' => "products[".$product_row['id']."][name]",
            'value' => $product_row['name'],
            'label' => trans('admin.name'),
            'no_laple'  => true,
        ])
    </td>
    <td>
        <select class="form-control select2 unit_change" name="products[{{$product_row['id']}}][unit_id]" required data-product_row_id="{{$product_row['id']}}">
            @foreach ($product_row['units'] as $unit)
                <option value="{{$unit['id']}}" data-multipler="{{$unit->getMultiplier()}}">{{$unit['actual_name']}}</option>
            @endforeach
        </select>   
    </td>
    <td>
        <input type="number" class="form-control product_row_quantity product_row_quantity_{{$product_row['id']}}"
                required name="products[{{$product_row['id']}}][quantity]" 
                value="{{$product_row['quantity']}}"
                data-product_row_id="{{$product_row['id']}}"
                min="1">
    </td>
    <td>
        @include('components.form.input', [
            'type' => 'number',
            'class' => 'form-control available_quantity_input_' . $product_row['id'],
            'attribute' => 'required readonly',
            'name' => "products[".$product_row['id']."][available_quantity]",
            'value' => $product_row['available_quantity'],
            'label' => trans('admin.available quantity'),
            'no_laple'  => true,
        ])
    </td>
    <td>
        <input type="number" class="form-control change_price product_row_unit_price_{{$product_row['id']}}"
            required name="products[{{$product_row['id']}}][unit_price]" 
            value="{{$product_row['purchase_price']}}"
            data-product_row_id="{{$product_row['id']}}">
    </td>
    <td>
        @include('components.form.input', [
            'type' => 'text',
            'class' => 'form-control product_row_total_'.$product_row['id'],
            'attribute' => 'required readonly',
            'name' => "products[".$product_row['id']."][total]",
            'value' => $product_row['total'],
            'label' => trans('admin.total'),
            'no_laple'  => true,
        ])
    </td>
    <td>
        <i class="fas fa-trash remove_row" style="color: #bd0d0d; cursor: pointer;" data-product_row_id="{{$product_row['id']}}"></i>
    </td>
</tr>
