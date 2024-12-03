@extends('layouts.blank')

@section('style')
    <style>
        /* Simple styling for results dropdown */
        .result-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
            border: 1px solid #ddd;
            max-height: 200px;
            overflow-y: auto;
            width: 100%;
        }

        .result-list li {
            padding: 8px;
            cursor: pointer;
        }

        .result-list li:focus {
            background-color: #837a7a;
            outline: none;
        }

        .list-group-item.disabled {
            pointer-events: none;
            /* Prevent any clicks */
            opacity: 0.5;
            /* Make it look disabled */
            background-color: #f8f9fa;
            /* Change background color if needed */
        }

        .result-list li:hover {
            background-color: #ddd;
        }

        table.dataTable {
            width: 100% !important;
            table-layout: fixed;
            /* Optional: Control column widths */
        }
    </style>
@endsection

@section('title', trans('admin.purchases'))
@section('content')
    @php
        $is_edit = false;
        if (isset($sell)) {
            $is_edit = true;
        }

        $disabled = '';
        if ($is_edit) {
            $disabled = 'disabled';
        }
        $product_segments = [];
        // Log::info($sell);
        if (isset($sell) && $sell->contact->salesSegment) {
            $product_segments = $sell->contact->salesSegment->products()->pluck('products.id');
        }
    @endphp

    <style>
        .modal-dialog {
            max-width: 1000px;
        }
    </style>
    <!-- form start -->
    <form id="purchase" method="post" action="{{ route('dashboard.purchases.store') }}">
        @csrf
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-1">
                    <div class="col-sm-12 d-flex align-items-center justify-content-end">

                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">{{ trans('admin.Home') }}</a>
                                / <a href="{{ route('dashboard.purchases.index') }}">{{ trans('admin.purchases') }}</a> /
                                {{ trans('admin.Create') }}</li>
                        </ol>
                    </div><!-- /.col -->
                </div>
                <div class="row mb-2">
                    <div class="col-sm-3">
                        <div class="col-lg-12">
                            @include('components.form.select', [
                                'collection' => $branches,
                                'id' => 'branch_id',
                                'index' => 'id',
                                'select' => isset($sell) ? $sell->branch_id : auth()->user()->branch_id,
                                'name' => 'branch_id',
                                'label' => trans('admin.branch') . ':',
                                'class' => 'form-control select2 branch_id w-100',
                                'attribute' => 'required ' . $disabled,
                            ])
                        </div>
                    </div><!-- /.col -->
                    <div class="col-sm-3">
                        <div class="col-lg-12">
                            @include('components.form.select', [
                                'collection' => $contacts,
                                'index' => 'id',
                                'id' => 'contact_id',
                                'select' => isset($sell) ? $sell->contact_id : $cash_contact?->id,
                                'name' => 'contact_id',
                                'label' => trans('admin.contact'),
                                'class' => 'form-control select2 contact_id',
                                'attribute' => 'required ' . $disabled,
                            ])
                        </div>
                    </div><!-- /.col -->
                    <div class="col-sm-3">

                        <label for="delivery_status">{{ trans('admin.Delivery-Status') }}</label>
                        <select name="delivery_status" id="delivery_status" class="form-control">
                            <option disabled selected>{{ trans('admin.Select-Delivery-Status') }}</option>
                            <option value="ordered">{{ trans('admin.Ordered') }}</option>
                            <option value="shipped">{{ trans('admin.Shipped') }}</option>
                            <option value="delivered">{{ trans('admin.Delivered') }}</option>
                        </select>

                    </div><!-- /.col -->
                    <div class="col-sm-3 d-flex align-items-center justify-content-end">

                        <button type="button" class="btn btn-success fire-popup ml-2" data-toggle="modal"
                            data-target="#getByBrand">{{ trans('admin.Add Bulck products') }}</button>

                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <section class="content ">
            @include('Dashboard.sells.parts.AddBulckProductsPopUp')
            <div class="container-fluid ">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12 ">
                        <!-- general form elements -->
                        <div class="card card-primary ">
                            <div class="card-header">

                            </div>
                            <!-- /.card-header -->

                            <div class="row px-5">

                                <div class="col-lg-12 py-2">
                                    <div>

                                        <input type="text" id="search" class="form-control"
                                            placeholder="ابحث عن المنتج ...." autocomplete="off" autofocus>
                                        <ul id="result-list" class="result-list list-group">
                                            <!-- Products will be appended here -->
                                        </ul>

                                    </div>
                                </div>


                                <div class="col-lg-12 my-3">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
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

                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="discount_type">{{ trans('admin.discount type') }}</label>
                                        <select class="form-control" id="discount_type" name="discount_type" required>
                                            <option value="percentage"
                                                {{ isset($sell) && $sell->discount_type == 'percentage' ? 'selected' : '' }}>
                                                {{ trans('admin.percentage') }}</option>
                                            <option value="fixed_price"
                                                {{ isset($sell) && $sell->discount_type == 'fixed_price' ? 'selected' : '' }}>
                                                {{ trans('admin.fixed amount') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="discount_value">{{ trans('admin.amount') }}</label>
                                        <input type="number" class="form-control" id="discount_value" name="discount_value"
                                            value="0" min="0" step="1" required>
                                    </div>
                                </div>

                                <div>
                                    <h5>الإجمالي النهائي: <span id="final_total" class="final_total">0.00</span></h5>

                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-success button-send" name="purchase_type"
                                    value="cash">{{ trans('admin.cash') }}</button>
                                <button type="submit" class="btn btn-primary button-send credit_button"
                                    name="purchase_type" value="credit">{{ trans('admin.credit') }}</button>
                                <button type="button" class="btn btn-primary  fire-popup" data-toggle="modal"
                                    data-target="#modal-default"
                                    data-url="{{ route('dashboard.purchases.multiPay') }}">{{ trans('admin.multi_pay') }}</button>
                            </div>

                        </div>
                        <!-- /.card -->
                    </div>
                </div><!-- /.container-fluid -->
        </section>
    </form>
@endsection

@section('script')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            let rowCounter = 1; // Counter to manage row numbering

            // Listen to the input event in the search field
            $('#search').on('input', function() {
                let query = $(this).val();
                let branchId = $('#branch_id').val();
                if (!branchId) {
                    $('#result-list').empty();
                    $('#result-list').append(
                        '<li tabindex="0" class="list-group-item text-danger">يرجى اختيار فرع أولاً</li>'
                        );
                    return;
                }

                $.ajax({
                    url: '{{ route('dashboard.sells.products.search') }}',
                    method: 'GET',
                    data: {
                        query: query,
                        branch_id: branchId
                    },
                    success: function(data) {
                        $('#result-list').empty();
                        if (data.length === 0) {
                            $('#result-list').append(
                                '<li tabindex="0" class="list-group-item">لا توجد منتجات مطابقة</li>'
                                );
                        } else {
                            $.each(data, function(index, product) {
                                const isAvailable = product.available_quantity = product
                                    .available_quantity;
                                $('#result-list').append(`
                                <li tabindex="0" class="list-group-item product-item"
                                    data-id="${product.id}"
                                    data-name="${product.name}"
                                    data-price="${product.purchase_price}"
                                    data-available-quantity="${product.available_quantity}"
                                    data-sku="${product.sku}"
                                    ${isAvailable ? '' : 'onclick="return false;"'} >
                                    ${product.name} - SKU: ${product.sku} - Price: ${product.purchase_price} - Available: ${product.available_quantity}
                                </li>
                            `);
                            });
                        }
                    },
                    error: function() {
                        $('#result-list').empty();
                        $('#result-list').append(
                            '<li class="list-group-item text-danger">حدث خطأ أثناء البحث</li>'
                            );
                    }
                });
            });

            // If change branch
            $('#branch_id').on('change', function() {
                $('#search').val('');
                $('#result-list').empty();
            });

            // ______________________________________________
            // Add Proudcts

            $(document).on('click', '.product-item', function() {
                let productId = $(this).data('id');
                let branchId = $('#branch_id').val();
                let contact_id = $('.contact_id').val();
                $.ajax({
                    url: '{{ route('dashboard.sells.products.row.add') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: productId,
                        branch_id: branchId,
                        contact_id: contact_id

                    },
                    success: function(response) {
                        let existingRow = $(`.sell_table tr[data-product-id="${response.id}"]`);
                        if (existingRow.length > 0) {
                            let quantityInput = existingRow.find('.quantity');
                            let currentQuantity = parseInt(quantityInput.val()) || 0;
                            let newQuantity = currentQuantity + 1;


                            quantityInput.val(newQuantity);
                            existingRow.find('.available-quantity').text(response
                                .available_quantity - newQuantity);
                            existingRow.find('.total').text((newQuantity * response
                                .purchase_price).toFixed(2));
                        } else {
                            let newQuantity = 1;
                            let available_quantity = response.available_quantity - newQuantity;

                            $('.sell_table').append(`
                            <tr data-product-id="${response.id}">
                            <td>${rowCounter++}</td>
                            <td>${response.name}</td>
                             <td>
                                 <select id="unit_id" class="form-control unit-select" name="products[${response.id}][unit_id]">
                                    ${response.units.map(unit => `<option value="${unit.id}"  data-multipler="${unit.multipler}"  ${response.unit == unit.id ? 'selected' : ''}>${unit.actual_name}</option>`).join('')}
                                </select>
                            <td>
                                <input type="number" class="form-control quantity" 
                                    name="products[${response.id}][quantity]" 
                                    value="${newQuantity}" min="1">
                            </td>
                            <td class="available-quantity">${response.available_quantity}</td>
                            <td>
                                <input id="unit_price" type="number" class="form-control unit-price" 
                                        name="products[${response.id}][unit_price]" 
                                        value="${response.segment_price ? response.segment_price :  response.purchase_price  }" min="0" step="1">
                            </td>
                            <td class="total">${(newQuantity * (response.purchase_price)).toFixed(2)}</td> <!-- Calculate total -->
                            <td><button type="button" class="btn btn-danger remove-product">حذف</button></td>

                                <input type="hidden" id="product_id" name="products[${response.id}][product_id]" value="${response.id}">
                                <input id="id" type="hidden" name="products[${response.id}][id]" value="${response.id}">
                                <input id="main_unit_price" type="hidden" class="main_unit_price_${response.id}" name="products[${response.id}][main_unit_price]" value="${response.purchase_price}">
                                <input id="main_available_quantity" type="hidden" class="main_available_quantity_${response.id}" name="products[${response.id}][main_available_quantity]" value="${response.available_quantity}">
                                <input id="'unit_multipler" type="hidden" class="unit_multipler_${response.id}" name="products[${response.id}][unit_multipler]" value="0">
                            </tr>
                        `);
                            scrollToBottom(); // Scroll to the last added row
                            calculateFinalTotal();
                        }

                        $('#search').val('');
                        $('#result-list').empty();
                        calculateFinalTotal();
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        alert('Error: ' + xhr.status + ' ' + xhr.statusText);
                    }
                });
            });
            $(document).on('change', '.unit-select', function() {
                var $row = $(this).closest('tr');
                var product_row_id = $row.data('product-id');
                var selectedUnitId = $(this).val();
                var branchId = $('#branch_id').val();
                var contactId = $('#contact_id').val();

                $.ajax({
                    url: '{{ route('dashboard.units.product.update') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: product_row_id,
                        unit_id: selectedUnitId,
                        branch_id: branchId,
                        contact_id: contactId,
                    },
                    success: function(response) {
                        if (response.success) {
                            var newUnitPrice = response.new_unit_price_purchase;
                            var quantityInput = $row.find('.quantity');
                            var quantity = parseInt(quantityInput.val()) || 0;

                            $row.find('.unit-price').val(newUnitPrice);

                            var total = (newUnitPrice * quantity).toFixed(2);
                            $row.find('.total').text(total);

                            $row.find('.unit_multipler_' + product_row_id).val(response
                                .unit_multipler);

                            var availableQuantity = response.available_quantity;

                            // تحديث عرض الكمية المتاحة في الصف الحالي
                            $row.find('.available-quantity').text(availableQuantity);

                            // حساب الإجمالي النهائي بعد التحديث
                            calculateFinalTotal();
                        } else {
                            alert('فشل في تحديث البيانات. حاول مرة أخرى.');
                        }
                    }.bind(this), // ربط this هنا
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        alert('Error: ' + xhr.status + ' ' + xhr.statusText);
                    }
                });
            });


            $(document).on('click', '.remove-product', function() {
                $(this).closest('tr').remove();
                calculateFinalTotal();
                updateRowNumbers(); // Update row numbers after removal
            });

            $(document).on('input', '.quantity', function() {
                let row = $(this).closest('tr');
                let quantity = parseInt($(this).val()) || 0;
                let unitPrice = parseFloat(row.find('.unit-price').val());
                let newTotal = (quantity * unitPrice);
                row.find('.total').text(newTotal.toFixed(2));
                calculateFinalTotal();
            });

            $(document).on('input', '.unit-price', function() {
                let row = $(this).closest('tr');
                let quantity = parseInt(row.find('.quantity').val()) || 0;
                let unitPrice = parseFloat($(this).val());
                let newTotal = (quantity * unitPrice);
                row.find('.total').text(newTotal.toFixed(2));
                calculateFinalTotal();
            });

            function calculateFinalTotal() {
                let finalTotal = 0;
                $('.sell_table .total').each(function() {
                    finalTotal += parseFloat($(this).text());
                });

                const discountType = $('#discount_type').val();
                let discountAmount = parseFloat($('#discount_value').val()) || 0;

                if (discountType === 'percentage') {
                    finalTotal -= (finalTotal * (discountAmount / 100));
                } else if (discountType === 'fixed_price') {
                    finalTotal -= discountAmount;
                }

                finalTotal = Math.max(finalTotal, 0);
                $('.final_total').text(finalTotal.toFixed(2));
            }


            $('#discount_type, #discount_value').on('input change', function() {
                calculateFinalTotal();

            });



            function updateRowNumbers() {
                let counter = 1;
                $('.sell_table tr').each(function() {
                    $(this).find('td:first').text(counter++);
                });
                calculateFinalTotal();
            }

            function scrollToBottom() {
                $("html, body").animate({
                    scrollTop: $(document).height()
                }, 1000);
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            // When the brand is changed, fetch the corresponding products
            $('#brand_id').on('change', function() {
                let brandId = $(this).val();
                let branchId = $('#branch_id').val();
                // Clear the table before loading new products
                $('.sell_table_AddBulckProducts').empty();
                $('.final_total').text('0.00');

                // Fetch products for the selected brand
                $.ajax({
                    url: '{{ route('dashboard.sells.products.getByBrand') }}', // Adjust this route accordingly
                    method: 'GET',
                    data: {
                        brand_id: brandId,
                        branch_id: branchId
                    },
                    success: function(products) {
                        console.log(products); // Log products for debugging
                        if (products.length === 0) {
                            $('.sell_table_AddBulckProducts').append(
                                '<tr><td colspan="7" class="text-center">لا توجد منتجات لهذا البرند</td></tr>'
                                );
                        } else {
                            // Append each product to the table
                            $.each(products, function(index, product) {
                                $('.sell_table_AddBulckProducts').append(
                                    `<tr data-product-id="${product.id}">
                                    <td>${product.name}</td>
                                    <td>
                                        <select class="form-control unit-select" name="products[${product.id}][unit_id]">
                                            ${product.units.map(unit => `<option value="${unit.id}" data-multipler="${unit.multipler}" ${product.unit == unit.id ? 'selected' : ''}>${unit.actual_name}</option>`).join('')}
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control quantity" 
                                            name="products[${product.id}][quantity]" 
                                            value="1" min="1" max="${product.available_quantity - 1}">
                                    </td>
                                    <td class="available-quantity">${product.available_quantity}</td>
                                    <td>
                                        <input type="number" class="form-control unit-price" 
                                            name="products[${product.id}][unit_price]" 
                                            value="${product.purchase_price}" min="0" step="0.01">
                                    </td>
                                    <td class="total">${(product.purchase_price).toFixed(2)}</td>
                                    <td><button type="button" class="btn btn-danger remove-product">حذف</button></td>
                                    <!-- Hidden Inputs Wrapped in <td> to ensure proper placement -->
                                    <td>
                                        <input type="hidden" name="products[${product.id}][product_id]" value="${product.id}">
                                        <input type="hidden" name="products[${product.id}][id]" value="${product.id}">
                                        <input type="hidden" class="main_unit_price_${product.id}" name="products[${product.id}][main_unit_price]" value="${product.purchase_price}">
                                        <input type="hidden" class="main_available_quantity_${product.id}" name="products[${product.id}][main_available_quantity]" value="${product.available_quantity}">
                                        <input type="hidden" class="unit_multipler_${product.id}" name="products[${product.id}][unit_multipler]" value="0">
                                    </td>
                                </tr>`
                                );
                            });
                        }
                        // Recalculate the final total after loading products
                        calculateFinalTotal();
                    },
                    error: function() {
                        $('.sell_table_AddBulckProducts').empty();
                        $('.sell_table_AddBulckProducts').append(
                            '<tr><td colspan="7" class="text-center text-danger">حدث خطأ أثناء تحميل المنتجات</td></tr>'
                            );
                    }
                });
            });

            // Add product to the main table
            $('.modal-footer .btn-primary').on('click', function() {
                let totalToAdd = 0; // Variable to track total to add
                $('.sell_table_AddBulckProducts tr').each(function() {
                    let row = $(this);
                    let productId = row.data('product-id');
                    let quantity = row.find('.quantity').val();
                    let unitPrice = row.find('.unit-price').val();
                    let total = (quantity * unitPrice).toFixed(
                    2); // Calculate the total for the current row

                    // Only add if quantity is greater than zero
                    if (quantity > 0) {
                        // Check if the product already exists in the main table
                        let existingRow = $('.sell_table tr[data-product-id="' + productId + '"]');
                        if (existingRow.length > 0) {
                            // If the product exists, update the quantity and total
                            let existingQuantity = parseInt(existingRow.find('.quantity').val()) ||
                                0;
                            let newQuantity = existingQuantity + parseInt(quantity);
                            existingRow.find('.quantity').val(newQuantity);
                            existingRow.find('.total').text((newQuantity * unitPrice).toFixed(2));
                        } else {
                            // If the product does not exist, append it to the main table
                            $('.sell_table').append(
                                `<tr data-product-id="${productId}">
                                <td class="row-number"></td> <!-- Placeholder for row number -->
                                <td>${row.find('td').eq(0).text()}</td>
                                <td>
                                    <select class="form-control unit-select" name="products[${productId}][unit_id]">
                                        ${row.find('.unit-select').html()} <!-- اختيار الوحدة من الصف -->
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control quantity" name="products[${productId}][quantity]" value="${quantity}" min="1">
                                </td>
                                <td class="available-quantity">${row.find('.available-quantity').text()}</td>
                                <td>
                                    <input type="number" class="form-control unit-price" name="products[${productId}][unit_price]" value="${unitPrice}" min="0" step="0.01">
                                </td>
                                <td class="total">${total}</td>
                                <td><button type="button" class="btn btn-danger remove-product">حذف</button></td>
                                <!-- Hidden Inputs Wrapped in <td> to ensure proper placement -->
                                <td>
                                    <input type="hidden" name="products[${productId}][product_id]" value="${productId}">
                                    <input type="hidden" name="products[${productId}][id]" value="${productId}">
                                    <input type="hidden" class="main_unit_price_${productId}" name="products[${productId}][main_unit_price]" value="${unitPrice}">
                                    <input type="hidden" class="main_available_quantity_${productId}" name="products[${productId}][main_available_quantity]" value="${row.find('.available-quantity').text()}">
                                    <input type="hidden" class="unit_multipler_${productId}" name="products[${productId}][unit_multipler]" value="0">
                                </td>
                            </tr>`
                            );
                        }

                        // Update total to add
                        totalToAdd += parseFloat(total); // Add total of this row to totalToAdd
                    }
                });

                // Update the final total
                if (totalToAdd > 0) {
                    calculateFinalTotal();
                }

                // Update row numbers after adding products
                updateRowNumbers();

                // Close the modal
                $('#getByBrand').modal('hide');

                // Recalculate the final total after adding products
                calculateFinalTotal();
            });

            function calculateFinalTotal() {
                let finalTotal = 0;
                $('.sell_table .total').each(function() {
                    finalTotal += parseFloat($(this).text());
                });

                const discountType = $('#discount_type').val();
                let discountAmount = parseFloat($('#discount_value').val()) || 0;

                if (discountType === 'percentage') {
                    finalTotal -= (finalTotal * (discountAmount / 100));
                } else if (discountType === 'fixed_price') {
                    finalTotal -= discountAmount;
                }

                finalTotal = Math.max(finalTotal, 0);
                $('.final_total').text(finalTotal.toFixed(2));
            }

            $('#discount_type, #discount_value').on('input change', function() {
                calculateFinalTotal();
            });

            // Update row numbers
            function updateRowNumbers() {
                $('.sell_table tr').each(function(index) {
                    $(this).find('.row-number').text(index + 1); // Update the row number
                });
            }

            // Update total price when quantity or unit price changes
            $(document).on('input', '.quantity, .unit-price', function() {
                let row = $(this).closest('tr');
                let quantity = parseInt(row.find('.quantity').val()) || 0;
                let unitPrice = parseFloat(row.find('.unit-price').val()) || 0;

                // Calculate the new total for the row
                let newTotal = (quantity * unitPrice);
                row.find('.total').text(newTotal.toFixed(2)); // Update the row's total

                // Recalculate the final total after the change
                calculateFinalTotal();
            });

            // Remove product row
            $(document).on('click', '.remove-product', function() {
                $(this).closest('tr').remove(); // Remove the row
                calculateFinalTotal(); // Recalculate the final total after removing a product

                // Update row numbers after removal
                updateRowNumbers();
            });

            // Clear the table when the modal is closed
            $('#getByBrand').on('hidden.bs.modal', function() {
                $('.sell_table_AddBulckProducts').empty(); // Clear the additional product table
            });
        });
    </script>

    {{-- create transaction sell and print it  debend on ajax request  --}}
    <script>
        $(document).ready(function() {

            $(document).on('click', '.button-send', function(e) {
                e.preventDefault();

                data = {};

                data._token = "{{ csrf_token() }}";
                data.branch_id = $('#branch_id').val();
                data.delivery_status = $('#delivery_status').val();
                var amount = $('#amount').val();
                data.amount = amount;


                if (!data.branch_id) {
                    toastr.error("اختر الفرع")
                    return;
                }
                data.contact_id = $('#contact_id').val();
                if (!data.contact_id) {
                    toastr.error("اختر العميل")
                    return;
                }
                data.discount_type = $('#discount_type').val();
                data.discount_value = $('#discount_value').val();
                data.final_total = $('#final_total').text();
                data.purchase_type = $(this).val();

                console.log(data);

                var products = [];
                tableRr = $('.sell_table tr');
                // console.log(products);
                tableRr.each(function(tr) {
                    let quantityInput = $(this).find('td .quantity');
                    let unit_priceInput = $(this).find('td #unit_price');
                    let productId = $(this).find('#product_id');
                    let unitId = $(this).find('#unit_id');
                    let main_unit_price = $(this).find('#main_unit_price');
                    let main_available_quantity = $(this).find('#main_available_quantity');
                    let unit_multipler = $(this).find('#unit_multipler');


                    let product = {};

                    product.quantity = quantityInput.val();
                    product.product_id = productId.val();
                    product.id = productId.val();
                    product.unit_price = unit_priceInput.val();
                    product.unit_id = unitId.val();
                    product.main_unit_price = main_available_quantity.val();
                    product.main_available_quantity = main_available_quantity.val();
                    product.unit_multipler = unit_multipler.val();
                    products.push(product)
                    console.log(product);
                    console.log(quantityInput.val());

                });

                if (products.length < 1) {
                    toastr.error("حدد المنتجات")
                    return;
                }

                data.products = products;
                console.log('==================');
                console.log(products);
                console.log('==================');
                $(this).prop('disabled', true);
                $.ajax({
                    url: "{{ route('dashboard.purchases.store') }}",
                    type: 'POST',
                    data: data,

                    success: function(response) {

                        console.log('================================');
                        console.log(response);
                        $('.sell_table').empty();
                        $('.final_total').text('0.00');
                        $('#delivery_status option:first').prop('selected', true);
                        $('#contact_id option:first').prop('selected', true);
                        $('#branch_id option:first').prop('selected', true);


                        toastr.success("success")

                    },
                    error: function(xhr, status, error) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;


                            $.each(errors, function(key, value) {
                                toastr.error(value[
                                    0]);
                            });
                        } else {
                            toastr.error("An error occurred, please try again.");
                        }

                    }
                });

                setTimeout(() => {
                    $(this).prop('disabled', false);
                }, 2000);
            })
            $('.my-popup').on('hidden.bs.modal', function() {
                $('.my-popup .modal-title').empty();
                $('.my-popup .modal-body').empty(); // $(this).empty();
            });

            $(document).on("keydown", function(event) {
                console.log('test from when etner action ');
                if (event.key === "Enter") {
                    event.preventDefault();
                    console.log('test from when etner action ');
                    if ($('#result-list li:focus').is(':focus')) {
                        $("#result-list li:focus").click();
                    }

                    // If #search is not focused, focus it
                    if (!$('#search').is(':focus')) {
                        $('#search').focus();
                    }
                }

                if (event.key === "ArrowDown") {
                    event.preventDefault();

                    let current = $("#result-list li:focus");
                    console.log('test from when arrow action ');
                    console.log(current);
                    if (current.length === 0) {
                        $("#result-list li").first().focus();
                    } else {
                        let next = current.next("li");
                        if (next.length) {
                            next.focus();
                        }
                    }
                }

                if (event.key === "ArrowUp") {
                    event.preventDefault();

                    let current = $("#result-list li:focus");

                    if (current.length === 0) {
                        $("#result-list li").last().focus();
                    } else {
                        let prev = current.prev("li");
                        if (prev.length) {
                            prev.focus();
                        }
                    }
                }
            });


        })
    </script>

@endsection
