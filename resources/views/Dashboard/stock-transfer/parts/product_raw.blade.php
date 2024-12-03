<tr class="product_row_{{$product_row['id']}}">
    <input type="hidden" name="products[{{$product_row['id']}}][id]" value="{{$product_row['id']}}">
    <input type="hidden" class="product_row_available_quantity_{{$product_row['id']}}" name="products[{{$product_row['id']}}][available_quantity]" value="{{$product_row['available_quantity']}}">
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
                value="0"
                data-product_row_id="{{$product_row['id']}}"
                min="0" max="{{$product_row['available_quantity']}}">
    </td>
    <td>
        <i class="fas fa-trash remove_row" style="color: #bd0d0d; cursor: pointer;" data-product_row_id="{{$product_row['id']}}"></i>
    </td>
</tr>
