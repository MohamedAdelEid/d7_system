<script type="text/javascript">
    //    Search 
    $(document).ready(function() {
        // Counter to manage row numbering
        let rowCounter = 1;
        // Listen to the input event in the search field
        $('#search').on('input', function() {
            let query = $(this).val();
            let branchId = $('#branch_id').val();
            let contact_id = $('.contact_id').val();
            if (!branchId) {
                $('#result-list').empty();
                $('#result-list').append(
                    '<li tabindex="0" class="list-group-item text-danger">يرجى اختيار فرع أولاً</li>'
                );
                return;
            }
            if (!contact_id) {
                $('#result-list').empty();
                $('#result-list').append(
                    '<li tabindex="0" class="list-group-item text-danger">يرجى اختيار جهة الاتصال </li>'
                );
                return;
            }
            $('.contact_id').on('change', function() {
                $('#search').val('');
                $('#result-list').empty();
            });
            $.ajax({
                url: '{{ route('dashboard.sells.products.search') }}',
                method: 'GET',
                data: {
                    query: query,
                    contact_id: contact_id,
                    branch_id: branchId
                },
                success: function(data) {
                    console.log(data);
                    console.log('test');
                    $('#result-list').empty();
                    if (data.length === 0) {
                        $('#result-list').append(
                            '<li class="list-group-item">لا توجد منتجات مطابقة</li>');
                    } else {
                        $.each(data, function(index, product) {
                            const isAvailable = product.available_quantity > 0;
                            $('#result-list').append(`
                                <li tabindex="0" class="list-group-item product-item ${isAvailable ? '' : 'disabled'}"
                                    data-id="${product.id}"
                                    data-name="${product.name}"
                                    data-price="${product.unit_price}"
                                    data-available-quantity="${product.available_quantity}"
                                    data-sku="${product.sku}"
                                    ${isAvailable ? '' : 'onclick="return false;"'}>
                                    <div class="d-flex justify-content-around text-start">
                                        <div class="p-1">
                                           SKU: ${product.sku} 
                                        </div>
                                        <div class="p-1">
                                           اسم المنتج :  ${product.name} 
                                        </div>
                                        <div class="p-1">
                                          سعر البيع: ${product.unit_price} 
                                       </div>
                                         <div class="p-1">
                                           سعر الشراء: ${product.purchase_price} 
                                        </div>
                                         <div class="p-1">
                                           الكمية المتاحة : ${product.available_quantity} 
                                        </div>
                                    </div>
                                </li>
                            `);
                        });
                    }
                },
                error: function() {
                    $('#result-list').empty();
                    $('#result-list').append(
                        '<li tabindex="0" class="list-group-item text-danger">حدث خطأ أثناء البحث</li>'
                    );
                }
            });
        });
        // If change branch
        $('#branch_id').on('change', function() {
            $('#search').val('');
            $('#result-list').empty();
        });
        // Add Products
        $(document).on('click', '.product-item', function() {
            let productId = $(this).data('id');
            let branchId = $('#branch_id').val();
            let contact_id = $('.contact_id').val();
            if (!contact_id) {
                $('.sell_table').append(
                    '<tr><td colspan="8" class="text-center">يرجى اختيار جهة الاتصال أولاً قبل إضافة المنتج. </td></tr>'
                );
            } else {
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
                        let existingRow = $(
                            `.sell_table tr[data-product-id="${response.id}"]`);
                        if (existingRow.length > 0) {
                            let quantityInput = existingRow.find('.quantity');
                            let currentQuantity = parseInt(quantityInput.val()) || 0;
                            let newQuantity = currentQuantity + 1;
                            quantityInput.val(newQuantity);
                            existingRow.find('.available-quantity').text(response
                                .available_quantity - newQuantity);
                            existingRow.find('.total').text((newQuantity * response
                                .segmentPrice).toFixed(
                                2)); // استخدم السعر المستند إلى الشريحة
                        } else {
                            let newQuantity = response.quantity;
                            let available_quantity = response.available_quantity -
                                newQuantity;
                            const max = response.available_quantity > response.max_sale ?
                                response.max_sale :
                                response.available_quantity;
                            const min = response.available_quantity > response.min_sale ?
                                response.min_sale :
                                response.available_quantity;

                            let lastPriceSale = response.last_sale_price ?  "اخر سعر شراء : " + response.last_sale_price : "";

                            $('.sell_table').append(`
                            <tr data-product-id="${response.id}">
                                <td>${rowCounter++}</td>
                                <td>${response.name}</td>
                                <td>
                                    <select id="unit_id" class="form-control unit-select" name="products[${response.id}][unit_id]">
                                        ${response.units.map(unit => `<option value="${unit.id}" data-multipler="${unit.multipler}" ${response.unit == unit.id ? 'selected' : ''}>${unit.actual_name}</option>`).join('')}
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control quantity" 
                                        name="products[${response.id}][quantity]" 
                                        value="${min}" min="${min}" max="${max}" required>
                                    <small class="error-message" style="color: red; display: none;"></small> <!-- Message area -->
                                </td>
                                <td class="available-quantity">${response.available_quantity - min}</td>
                                <td>
                                    <input id="unit_price" type="number" class="form-control unit-price" 
                                        name="products[${response.id}][unit_price]" 
                                        value="${response.segmentPrice}" min="0" step="1"> 
                                        <p class="last-price-sale text-primary">${lastPriceSale}</p>
                                </td>
                                <td  class="total">${(newQuantity * response.segmentPrice).toFixed(2)}</td> <!-- استخدم السعر المستند إلى الشريحة -->
                                <td><button type="button" class="btn btn-danger remove-product">حذف</button></td>
                                <input type="hidden" id="product_id" name="products[${response.id}][product_id]" value="${response.id}">
                                <input type="hidden" name="products[${response.id}][id]" value="${response.id}">
                                <input type="hidden" id="main_unit_price" class="main_unit_price_${response.id}" name="products[${response.id}][main_unit_price]" value="${response.segmentPrice}">
                                <input type="hidden" id="main_available_quantity" class="main_available_quantity_${response.id}" name="products[${response.id}][main_available_quantity]" value="${response.available_quantity}">
                                <input type="hidden" id="unit_multipler" class="unit_multipler_${response.id}" name="products[${response.id}][unit_multipler]" value="0">
                            </tr>
                        `);

                            scrollToBottom(); // Scroll to the last added row
                            calculateFinalTotal();
                        }

                        $('#search').val('');
                        $('#result-list').empty();
                        calculateFinalTotal();

                        // Real-time validation for quantity input
                        $('.quantity').on('input', function() {
                            const min = parseInt($(this).attr('min'));
                            const max = parseInt($(this).attr('max'));
                            const quantity = parseInt($(this).val());
                            const errorMessage = $(this).next('.error-message');
                            // Reset error message
                            errorMessage.hide();
                            errorMessage.text('');

                            if (quantity < min || quantity > max) {
                                errorMessage.text(
                                    `الكمية يجب أن تكون بين ${min} و ${max}`);
                                errorMessage.show();
                                $(this).css('border-color', 'red');
                            } else {
                                $(this).css('border-color',
                                    ''); // Reset border color if valid
                            }
                        });


                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        alert('Error: ' + xhr.status + ' ' + xhr.statusText);
                    }
                });

                updateRowNumbers()
                calculateFinalTotal();

            }

        });
        $(document).on('input', '.quantity', function() {
            let row = $(this).closest('tr');
            let quantity = $(this).val();
            let initialAvailable = row.find('#main_available_quantity').val();
            let availableQuantity = initialAvailable - quantity;
            row.find('.available-quantity').text(availableQuantity);
            console.log(quantity);
            console.log(row.find('#main_available_quantity'));
            console.log(initialAvailable);
            console.log(availableQuantity);
        });
        // Change Unit
        $(document).on('change', '.unit-select', function() {
            var $row = $(this).closest('tr'); // تحديد الصف الحالي
            var product_row_id = $row.data('product-id'); // جلب ID الصف الحالي
            var selectedUnitId = $(this).val(); // جلب ID الوحدة المختارة
            var branchId = $('#branch_id').val(); // جلب ID الفرع إذا كان ضروريًا
            let contact_id = $('.contact_id').val();


            $.ajax({
                url: '{{ route('dashboard.units.product.update') }}', // مسار تحديث الوحدة
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: product_row_id,
                    unit_id: selectedUnitId,
                    contact_id: contact_id,
                    branch_id: branchId // أضف المعلمات الأخرى حسب الحاجة
                },
                success: function(response) {
                    // تأكد من أن الاستجابة تحتوي على السعر والمعلومات الأخرى
                    if (response.success) {
                        // تحديث السعر ومعلومات أخرى
                        var newUnitPrice = response.new_unit_price; // السعر الجديد
                        var quantityInput = $row.find('.quantity'); // جلب حقل الكمية في الصف الحالي
                        var quantity = parseInt(quantityInput.val()) || 0;

                        // تحديث حقل السعر في الصف الحالي
                        $row.find('.unit-price').val(newUnitPrice);

                        // حساب الإجمالي في الصف الحالي
                        var total = (newUnitPrice * quantity).toFixed(2);

                        $row.find('.total').text(total);

                        // تحديث الحقل المخفي لمضاعف الوحدة في الصف الحالي
                        $row.find('.unit_multipler_' + product_row_id).val(response
                            .unit_multipler);
                        quantityInput.attr('min', response.min_sale);
                        quantityInput.attr('max', response.max_sale);
                        // تحديث الكمية المتاحة
                        var availableQuantity = response.available_quantity;
                        console.log(availableQuantity);
                        // إذا كانت الكمية المدخلة أكبر من الكمية المتاحة، أعد تعيينها
                        if (quantity > availableQuantity) {
                            alert(
                                'الكمية المدخلة تتجاوز الكمية المتاحة. تم إعادة تعيين الكمية إلى الكمية المتاحة.'
                            );
                            quantityInput.val(availableQuantity);
                            total = (newUnitPrice).toFixed(2);
                            $row.find('.total').text(total);
                        }

                        // تحديث عرض الكمية المتاحة في الصف الحالي
                        $row.find('.available-quantity').text(availableQuantity);
                        $row.find('#main_available_quantity').val(availableQuantity);
                        if (response.last_sale_price) {
                            $row.find('.last-price-sale').text(`اخر سعر شراء : ${response.last_sale_price}`);
                        } else {
                            $row.find('.last-price-sale').text('');
                        }
                       
                        // حساب الإجمالي النهائي بعد التحديث
                        calculateFinalTotal();
                        updateRowNumbers()
                        calculateTotal();
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
        $(document).on('input', '.unit-price', function() {
            let row = $(this).closest('tr');
            let quantity = parseInt(row.find('.quantity').val()) || 0;
            let unitPrice = parseFloat($(this).val());
            let newTotal = (quantity * unitPrice);
            row.find('.total').text(newTotal.toFixed(2));
            calculateFinalTotal();
        });
        // Remove
        $(document).on('click', '.remove-product', function() {
            $(this).closest('tr').remove();
            calculateFinalTotal();

            updateRowNumbers(); // Update row numbers after removal
        });

        // Credit
        $(document).on("change", ".contact_id ,.quantity", function() {
            var contact_id = $(".contact_id").val();
            var branch_id = $(".branch_id").val();
            calculateTotal()
            calculateFinalTotal();

            var final_total = Math.trunc($("#final_total").val());

            $.ajax({
                method: "GET",
                url: "{{ route('dashboard.contacts.ContctCreditLimit') }}",
                data: {
                    contact_id: contact_id
                },
                success: function(data) {

                    if (data > 0) {
                        $(".credit_button").show();

                        $(".credit_limit").text(data);
                    } else {

                        $(".credit_button").hide();
                        $(".credit_limit").text(0);
                    }
                }
            });
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
            $('#final_total').val(finalTotal.toFixed(2));

        }

        function calculateTotal() {

            $('.sell_table tr').each(function() {
                let quantity = parseFloat($(this).find('.quantity').val()) || 0;
                let unitPrice = parseFloat($(this).find('.unit-price').val()) || 0;

                let total = quantity * unitPrice;

                $(this).find('.total').text(total.toFixed(2));

            });


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

    $(document).ready(function() {
        // عند تغيير البراند، جلب المنتجات المتناسبة
        $('#brand_id').on('change', function() {
            let brandId = $(this).val();
            let branchId = $('#branch_id').val();
            let contactId = $('.contact_id').val();

            // مسح الجدول قبل تحميل منتجات جديدة
            $('.sell_table_AddBulckProducts').empty();

            // جلب المنتجات للبراند المحدد
            $.ajax({
                url: '{{ route('dashboard.sells.products.getByBrand') }}', // تعديل هذا المسار حسب الحاجة
                method: 'GET',
                data: {
                    brand_id: brandId,
                    branch_id: branchId,
                    contact_id: contactId
                },
                success: function(products) {
                    if (!contactId) {
                        $('.sell_table_AddBulckProducts').append(
                            '<tr><td colspan="7" class="text-center">يرجى اختيار جهة الاتصال أولاً قبل إضافة المنتج.</td></tr>'
                        );
                    } else {
                        if (products.length === 0) {
                            $('.sell_table_AddBulckProducts').append(
                                '<tr><td colspan="7" class="text-center">لا توجد منتجات لهذا البرند</td></tr>'
                            );
                        } else {
                            // إضافة كل منتج إلى الجدول
                            $.each(products, function(index, product) {
                                if (typeof product.unit_price !== 'number') {
                                    console.error(
                                        `Product ID ${product.id} has an invalid unit_price:`,
                                        product.unit_price
                                    );
                                    product.unit_price =
                                        0; // Default to 0 if invalid
                                }

                                const max = Math.min(product.available_quantity,
                                    product.max_sale);
                                const min = Math.min(product.available_quantity,
                                    product.min_sale);

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
                                            value="1" min="1" max="${max}" required>
                                    </td>
                                    <td class="available-quantity">${product.available_quantity}</td>
                                    <td>
                                        <input type="number" class="form-control unit-price" 
                                            name="products[${product.id}][unit_price]" 
                                            value="${product.unit_price.toFixed(2)}" min="0" step="0.01">
                                    </td>
                                    <td class="total">${(product.unit_price).toFixed(2)}</td>
                                    <td><button type="button" class="btn btn-danger remove-product">حذف</button></td>
                                    <td>
                                        <input type="hidden" name="products[${product.id}][product_id]" value="${product.id}">
                                        <input type="hidden" name="products[${product.id}][id]" value="${product.id}">
                                        <input type="hidden" class="main_unit_price_${product.id}" name="products[${product.id}][main_unit_price]" value="${product.unit_price}">
                                        <input type="hidden" class="main_available_quantity_${product.id}" name="products[${product.id}][main_available_quantity]" value="${product.available_quantity}">
                                        <input type="hidden" class="unit_multipler_${product.id}" name="products[${product.id}][unit_multipler]" value="${product.units[0].multipler || 0}">
                                    </td>
                                </tr>`
                                );
                            });
                        }
                    }
                    // إعادة حساب الإجمالي بعد تحميل المنتجات
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

        // عندما تتغير الكمية أو السعر، إعادة حساب الإجمالي
        $(document).on('input', '.quantity, .unit-price', function() {
            let row = $(this).closest('tr');
            let quantity = parseInt(row.find('.quantity').val()) || 0;
            let unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
            let newTotal = (quantity * unitPrice);
            row.find('.total').text(newTotal.toFixed(2));
            calculateFinalTotal(); // إعادة حساب الإجمالي النهائي
        });
        $('.modal-footer .btn-primary').on('click', function() {
            let totalToAdd = 0;
            $('.sell_table_AddBulckProducts tr').each(function() {
                let row = $(this);
                let productId = row.data('product-id');
                let quantity = parseInt(row.find('.quantity').val()) || 0;
                let unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
                let total = (quantity * unitPrice).toFixed(2);
                let max = parseInt(row.find('.quantity').attr('max'));
                let min = parseInt(row.find('.quantity').attr('min')) || 1;

                if (quantity > 0) {
                    let existingRow = $('.sell_table tr[data-product-id="' + productId + '"]');
                    if (existingRow.length > 0) {
                        let existingQuantity = parseInt(existingRow.find('.quantity').val()) ||
                            0;
                        let newQuantity = existingQuantity + quantity;
                        existingRow.find('.quantity').val(newQuantity);
                        existingRow.find('.total').text((newQuantity * unitPrice).toFixed(2));
                    } else {
                        $('.sell_table').append(
                            `<tr data-product-id="${productId}">
                        <td class="row-number"></td>
                        <td>${row.find('td').eq(0).text()}</td>
                        <td>
                            <select class="form-control unit-select" name="products[${productId}][unit_id]">
                                ${row.find('.unit-select').html()}
                            </select>
                        </td>
                        <td>
                            <input type="number" class="form-control quantity" name="products[${productId}][quantity]" value="${quantity}" min="${min}" max="${max}" required>
                        </td>
                        <td class="available-quantity">${row.find('.available-quantity').text()}</td>
                        <td>
                            <input type="number" class="form-control unit-price" name="products[${productId}][unit_price]" value="${unitPrice.toFixed(2)}" min="0" step="0.01">
                        </td>
                        <td class="total">${(quantity * unitPrice).toFixed(2)}</td>
                        <td><button type="button" class="btn btn-danger remove-product">حذف</button></td>
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
                    totalToAdd += parseFloat(total);
                }
            });

            if (totalToAdd > 0) {
                calculateFinalTotal();
            }

            updateRowNumbers();

            // أغلق المودال وأفرغ محتوى الجدول داخله
            $('#getByBrand').modal('hide');
            $('.sell_table_AddBulckProducts').empty(); // هذا السطر يفرغ محتوى الجدول عند إغلاق المودال
        });


        // حساب الإجمالي النهائي
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

        // تحديث أرقام الصفوف
        function updateRowNumbers() {
            $('.sell_table tr').each(function(index) {
                $(this).find('.row-number').text(index + 1);
            });
        }

        // إزالة منتج من الجدول
        $(document).on('click', '.remove-product', function() {
            $(this).closest('tr').remove();
            calculateFinalTotal();
        });
    });
</script>
