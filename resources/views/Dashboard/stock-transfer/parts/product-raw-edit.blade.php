<tr class="product_row_{{$product_row['id']}}">
    <input type="hidden" name="products[{{$product_row['id']}}][id]" value="{{$product_row['id']}}">
    <td>
        {{$product_row['name']}}
    </td>
    <td>
        <select class="form-control select2 unit_change" name="products[{{$product_row['id']}}][unit_id]" required data-product_row_id="{{$product_row['id']}}">
            @foreach ($product_row['units'] as $unit)
                <option value="{{$unit['id']}}" data-multipler="{{$unit->getMultiplier()}}">{{$unit['actual_name']}}</option>
            @endforeach
        </select>   
    </td>
    <td>
        {{$product_row['available_quantity']}}
    </td>
    <td>
        <input type="number" class="form-control product_row_quantity product_row_quantity_{{$product_row['id']}}"
                required name="products[{{$product_row['id']}}][quantity]" 
                value="{{$product_row['quantity'] ?? 0}}"
                min="1" max="{{$product_row['available_quantity'] + ($product_row['quantity'] ?? 0)}}">
    </td>
    <td>
        <button type="button" class="btn btn-danger btn-sm remove-line">{{ trans('admin.Delete') }}</button>
    </td>
</tr>