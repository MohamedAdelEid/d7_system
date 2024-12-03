<tr class="product_row_{{ $product_row['id'] }}">
    <input type="hidden" id="product_id" name="products[{{ $product_row['id'] }}][product_id]" value="{{ $product_row['id'] }}">
    <input type="hidden" name="products[{{ $product_row['id'] }}][id]" value="{{ $product_row['id'] }}">
    <input type="hidden" class="main_unit_price_{{ $product_row['id'] }}" name="products[{{ $product_row['id'] }}][main_unit_price]" value="{{ $product_row['unit_price'] }}">
    <input type="hidden" class="main_available_quantity_{{ $product_row['id'] }}" name="products[{{ $product_row['id'] }}][main_available_quantity]" value="{{ $product_row['available_quantity'] }}">
    <input type="hidden" class="unit_multipler_{{ $product_row['id'] }}" name="products[{{ $product_row['id'] }}][unit_multipler]" value="0">
    
    <!-- Product Name -->
    <td>
        <input type="text" class="form-control" name="products[{{ $product_row['id'] }}][name]" value="{{ $product_row['name'] }}" readonly>
    </td>
    
    <!-- Unit Selection -->
    <td>
        <select class="form-control select2 unit_change" name="products[{{ $product_row['id'] }}][unit_id]" required data-product_row_id="{{ $product_row['id'] }}">
            @foreach ($product_row['units'] as $unit)
                <option value="{{ $unit['id'] }}" data-multipler="{{ $unit->getMultiplier() }}">{{ $unit['actual_name'] }}</option>
            @endforeach
        </select>
    </td>
    
    <!-- Quantity Input (Start from 1) -->
    <td>
        <input type="number" class="form-control product_row_quantity product_row_quantity_{{ $product_row['id'] }}"
               name="products[{{ $product_row['id'] }}][quantity]"
               value="{{ $product_row['quantity'] }}" min="1" max="{{ $product_row['available_quantity'] }}"
               data-product_row_id="{{ $product_row['id'] }}">
    </td>

    <!-- Available Quantity (Read-only) -->
    <td>
        <input type="number" class="form-control available_quantity_input_{{ $product_row['id'] }}" 
               name="products[{{ $product_row['id'] }}][available_quantity]" 
               value="{{ $product_row['available_quantity'] }}" readonly>
    </td>
    
    <!-- Unit Price -->
    <td>
        <input type="number" id="unit_price" class="form-control product_row_unit_price_{{ $product_row['id'] }}" 
               name="products[{{ $product_row['id'] }}][unit_price]" 
               value="{{ $product_row['unit_price'] }}"
               data-product_row_id="{{ $product_row['id'] }}">
    </td>
    
    <!-- Total for the Row -->
    <td>
        <input type="text" class="form-control product_row_total product_row_total_{{ $product_row['id'] }}"
               name="products[{{ $product_row['id'] }}][total]"
               value="{{ $product_row['unit_price'] * 1 }}" readonly>
    </td>

    <!-- Remove Row Icon -->
    <td>
        <i class="fas fa-trash remove_row" style="color: #bd0d0d; cursor: pointer;" data-product_row_id="{{ $product_row['id'] }}"></i>
    </td>
</tr>
