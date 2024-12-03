@extends('layouts.admin')
@section('style')
    <style>
        /* Simple styling for results dropdown */
        #print-section {
            display: none;
        }

        @media print {
            body * {
                visibility: hidden;

            }

            #print-section * {
                visibility: visible;

            }

            #print-section {
                display: block;

            }
        }

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
    </style>
@endsection

@section('title', trans('admin.sells'))
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
    <form method="post" action="">
        @csrf
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ trans('admin.stock_transfers') }}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">{{ trans('admin.Home') }}</a>
                                / <a
                                    href="{{ route('dashboard.stock-transfers.index') }}">{{ trans('admin.stock_transfers') }}</a>
                                / {{ trans('admin.Create') }}</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
                <div class="row my-2">
                    <div class="col-lg-3">
                        @include('components.form.select', [
                            'collection' => $branches,
                            'index' => 'id',
                            'select' => isset($data) ? $data->from_branch_id : auth()->user()->branch_id,
                            'name' => 'from_branch_id',
                            'label' => trans('admin.from_branch'),
                            'class' => 'form-control select2 from_branch_id branch-select',
                            'attribute' => 'required',
                            'id' => 'from_branch_id', // إضافة المعرف
                        ])
                    </div>
                    <div class="col-lg-3">
                        @include('components.form.select', [
                            'collection' => $branches,
                            'index' => 'id',
                            'select' => isset($data) ? $data->to_branch_id : old('to_branch_id'),
                            'name' => 'to_branch_id',
                            'label' => trans('admin.to_branch'),
                            'class' => 'form-control select2 to_branch_id branch-select',
                            'attribute' => 'required',
                            'id' => 'to_branch_id', // إضافة المعرف
                        ])
                    </div>

                    <div class="col-lg-3">
                        <label>{{ trans('admin.status') }}</label>
                        <select class="form-control select2" id="contactType" name="status">
                            <option value="">{{ trans('admin.Select') }}</option>
                            <option value="pending">{{ trans('admin.pending') }}</option>
                            <option value="final">{{ trans('admin.final') }}</option>
                        </select>
                    </div>
                    <div class="col-sm-3 d-flex align-items-center justify-content-end">

                        <button type="button" class="btn btn-success fire-popup ml-2" data-toggle="modal"
                            data-target="#getByBrand">{{ trans('admin.Add Bulck products') }}</button>

                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <section class="content ">
            <div id="print-section">
            </div>
            @include('Dashboard.stock-transfer.parts.AddBulckProductsPopUp')
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
                                    @if ($errors->any())
                                        <div class="alert alert-danger m-2" role="alert">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ trans('admin.name') }}</th>
                                                <th style="min-width: 120px;">{{ trans('admin.unit') }}</th>
                                                <th>{{ trans('admin.quantity') }}</th>
                                                <th>{{ trans('admin.available quantity') }}</th>
                                                <th>{{ trans('admin.action') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody class="sell_table">

                                        </tbody>
                                    </table>
                                </div>



                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-success">{{ trans('admin.create') }}</button>
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
        document.addEventListener('DOMContentLoaded', function() {
            const fromBranchSelect = document.getElementById('from_branch_id');
            const toBranchSelect = document.getElementById('to_branch_id');

            function updateToBranchOptions() {
                const selectedFromBranch = fromBranchSelect.value;
                const toBranchOptions = toBranchSelect.querySelectorAll('option');

                toBranchOptions.forEach(option => {
                    if (option.value === selectedFromBranch) {
                        option.style.display = 'none';
                    } else {
                        option.style.display = 'block';
                    }
                });

                if (toBranchSelect.value === selectedFromBranch) {
                    toBranchSelect.value = '';
                }
            }

            fromBranchSelect.addEventListener('change', updateToBranchOptions);

            updateToBranchOptions();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const settings = {
                allow_unit_price_update: {{ session('allow_unit_price_update') ? 'true' : 'false' }},
            };

            const priceInputs = document.querySelectorAll('.unit-price-input');
            priceInputs.forEach(input => {
                input.disabled = !settings.allow_unit_price_update;
            });
        });
    </script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <script type="text/javascript">
        //    Search 
        $(document).ready(function() {
            // Counter to manage row numbering
            let rowCounter = 1;

            // Listen to the input event in the search field
            $('#search').on('input', function() {
                let query = $(this).val();
                let branchId = $('.from_branch_id').val();

                if (!branchId) {
                    displayMessage('يرجى اختيار فرع أولاً', 'text-danger');
                    return;
                }

                searchProducts(query, branchId);
            });

            // Handle branch change
            $('.from_branch_id').on('change', function() {
                clearSearchResults();
                clearAddedProducts();
            });

            // Add products to the table on product item click
            $(document).on('click', '.product-item', function() {
                let productId = $(this).data('id');
                let branchId = $('.from_branch_id').val();
                addProductToTable(productId, branchId);
            });

            // Handle unit change
            $(document).on('change', '.unit-select', function() {
                let row = $(this).closest('tr');
                let productRowId = row.data('product-id');
                let selectedUnitId = $(this).val();
                let branchId = $('.from_branch_id').val();

                updateProductUnit(productRowId, selectedUnitId, branchId, row);
            });

            // Handle quantity input
            $(document).on('input', '.quantity', function() {
                let row = $(this).closest('tr');
                let quantity = parseInt($(this).val()) || 0;
                updateAvailableQuantity(row, quantity);
                console.log('test from input quanitty');
            });

            // Remove product from table
            $(document).on('click', '.remove-product', function() {
                $(this).closest('tr').remove();
                updateRowNumbers();
            });

            function displayMessage(message, className) {
                $('#result-list').empty().append(`<li class="list-group-item ${className}">${message}</li>`);
            }

            function searchProducts(query, branchId) {
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
                            displayMessage('لا توجد منتجات مطابقة', '');
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
                                    <div class="p-1">SKU: ${product.sku}</div>
                                    <div class="p-1">اسم المنتج: ${product.name}</div>
                                    <div class="p-1">الكمية المتاحة: ${product.available_quantity}</div>
                                </div>
                            </li>
                        `);
                            });
                        }
                    },
                    error: function() {
                        displayMessage('حدث خطأ أثناء البحث', 'text-danger');
                    }
                });
            }

            function addProductToTable(productId, branchId) {
                $.ajax({
                    url: '{{ route('dashboard.sells.stock.products.row.add') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: productId,
                        branch_id: branchId,
                    },
                    success: function(response) {
                        let existingRow = $(`.sell_table tr[data-product-id="${response.id}"]`);
                        if (existingRow.length > 0) {
                            updateExistingRow(existingRow, response);
                        } else {
                            addNewRow(response);
                        }
                        clearSearchResults();
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert(`Error: ${xhr.status} ${xhr.statusText}`);
                    }
                });
            }

            function updateExistingRow(row, product) {
                let quantityInput = row.find('.quantity');
                let currentQuantity = parseInt(quantityInput.val()) || 0;
                let newQuantity = currentQuantity + 1;
                quantityInput.val(newQuantity);
                row.find('.available-quantity').text(product.available_quantity - newQuantity);
            }

            function addNewRow(product) {
                $('.sell_table').append(`
            <tr data-product-id="${product.id}">
                <td>${rowCounter++}</td>
                <td>${product.name}</td>
                <td>
                    <select class="form-control unit-select" name="products[${product.id}][unit_id]">
                        ${product.units.map(unit => `<option value="${unit.id}" data-multipler="${unit.multipler}" ${product.unit == unit.id ? 'selected' : ''}>${unit.actual_name}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control quantity" name="products[${product.id}][quantity]" value="1" min="1" max="${product.available_quantity}" required>
                    <small class="error-message" style="color: red; display: none;"></small>
                </td>
                <td class="available-quantity">${product.available_quantity}</td>
                <td><button type="button" class="btn btn-danger remove-product">حذف</button></td>
                <input type="hidden" name="products[${product.id}][product_id]" value="${product.id}">
                <input type="hidden" name="products[${product.id}][id]" value="${product.id}">
                <input type="hidden" id="main_available_quantity" class="main_available_quantity_${product.id}" name="products[${product.id}][main_available_quantity]" value="${product.available_quantity}">
                <input type="hidden" class="unit_multipler_${product.id}" name="products[${product.id}][unit_multipler]" value="0">
            </tr>
        `);
                scrollToBottom();
            }

            function updateProductUnit(productId, unitId, branchId, row) {
                $.ajax({
                    url: '{{ route('dashboard.units.stock.product.update') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: productId,
                        unit_id: unitId,
                        branch_id: branchId
                    },
                    success: function(response) {
                        console.log(response);
                        console.log('test');
                        var quantityInput = row.find('.quantity');
                        var quantity = parseInt(quantityInput.val()) || 0;

                        row.find('.unit_multipler_' + productId).val(response.unit_multipler);
                        quantityInput.attr('min', response.min_sale);
                        quantityInput.attr('max',  response.available_quantity);
                        var availableQuantity = response.available_quantity;
                        if (quantity > availableQuantity) {
                            alert(
                                'الكمية المدخلة تتجاوز الكمية المتاحة. تم إعادة تعيين الكمية إلى الكمية المتاحة.');
                            quantityInput.val(availableQuantity);
                        }

                        row.find('.available-quantity').text(availableQuantity);
                        row.find('#main_available_quantity').val(availableQuantity);
                        console.log( row.find('#main_available_quantity'));
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert(`Error: ${xhr.status} ${xhr.statusText}`);
                    }
                });
            }

            function updateAvailableQuantity(row, quantity) {
                let mainAvailableQuantity = parseFloat(row.find('.main_available_quantity_' + row.data('product-id'))
                    .val()) || 0;
                row.find('.available-quantity').text(mainAvailableQuantity - quantity);
            }

            function clearSearchResults() {
                $('#search').val('');
                $('#result-list').empty();
            }

            function clearAddedProducts() {
                $('.sell_table').empty();
            }

            function scrollToBottom() {
                $("html, body").animate({
                    scrollTop: $(document).height()
                }, 1000);
            }

            function updateRowNumbers() {
                let counter = 1;
                $('.sell_table tr').each(function() {
                    $(this).find('td:first').text(counter++);
                });
            }
        });

        $(document).ready(function() {
            $('#brand_id').on('change', function() {
                let brandId = $(this).val();
                let branchId = $('.from_branch_id').val();

                if (!branchId) {
                    $('.sell_table_AddBulckProducts').empty();
                    $('.sell_table_AddBulckProducts').append(
                        '<tr><td colspan="5" class="text-center text-danger"> يجب اختيار الفرع اولا</td></tr>'
                    );
                } else {
                    $('.sell_table_AddBulckProducts').empty();
                    $.ajax({
                        url: '{{ route('dashboard.sells.products.getByBrand') }}', // تعديل هذا المسار حسب الحاجة
                        method: 'GET',
                        data: {
                            brand_id: brandId,
                            branch_id: branchId
                        },
                        success: function(products) {
                            console.log(products);


                            if (products.length === 0) {
                                $('.sell_table_AddBulckProducts').append(
                                    '<tr><td colspan="5" class="text-center">لا توجد منتجات لهذا البرند</td></tr>'
                                );
                            } else {

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
                                                    value="1" min="1" max="${product.available_quantity}" required>
                                            </td>
                                            <td class="available-quantity">${product.available_quantity}</td>
                                            <td><button type="button" class="btn btn-danger remove-product">حذف</button></td>
                                            <td>
                                                <input type="hidden" name="products[${product.id}][product_id]" value="${product.id}">
                                                <input type="hidden" name="products[${product.id}][id]" value="${product.id}">
                                                <input type="hidden" class="main_available_quantity_${product.id}" name="products[${product.id}][main_available_quantity]" value="${product.available_quantity}">
                                                <input type="hidden" class="unit_multipler_${product.id}" name="products[${product.id}][unit_multipler]" value="0">
                                            </td>
                                        </tr>`
                                    );

                                });
                            }


                        },
                        error: function() {
                            $('.sell_table_AddBulckProducts').empty();
                            $('.sell_table_AddBulckProducts').append(
                                '<tr><td colspan="7" class="text-center text-danger">حدث خطأ أثناء تحميل المنتجات</td></tr>'
                            );
                        }
                    });
                }

            });




        });
        $('.modal-footer .btn-primary').on('click', function() {
            let totalToAdd = 0;
            $('.sell_table_AddBulckProducts tr').each(function() {
                let row = $(this);
                let productId = row.data('product-id');
                let quantity = parseInt(row.find('.quantity').val()) || 0;
                let max = parseInt(row.find('.quantity').attr('max'));
                if (quantity > 0) {
                    let existingRow = $('.sell_table tr[data-product-id="' + productId + '"]');
                    if (existingRow.length > 0) {
                        let existingQuantity = parseInt(existingRow.find('.quantity').val()) || 0;
                        let newQuantity = existingQuantity + quantity;

                        existingRow.find('.quantity').val(newQuantity);
                    } else {
                        $('.sell_table').append(
                            `<tr data-product-id="${productId}">
                                    <td class="row-number"></td> <!-- مكان للرقم الصف -->
                                    <td>${row.find('td').eq(0).text()}</td>
                                    <td>
                                        <select class="form-control unit-select" name="products[${productId}][unit_id]">
                                            ${row.find('.unit-select').html()}
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control quantity" name="products[${productId}][quantity]" value="${quantity}" min="1" max="${max}" required>
                                    </td>
                                    <td class="available-quantity">${row.find('.available-quantity').text()}</td>
                                    <td><button type="button" class="btn btn-danger remove-product">حذف</button></td>                            <td>
                                        <input type="hidden" name="products[${productId}][product_id]" value="${productId}">
                                        <input type="hidden" name="products[${productId}][id]" value="${productId}">
                                        <input type="hidden" class="main_available_quantity_${productId}" name="products[${productId}][main_available_quantity]" value="${row.find('.available-quantity').text()}">
                                        <input type="hidden" class="unit_multipler_${productId}" name="products[${productId}][unit_multipler]" value="0">
                                    </td>
                                </tr>`
                        );
                    }

                }
            });


            updateRowNumbers();

            $('#getByBrand').modal('hide');

        });

        function updateRowNumbers() {
            $('.sell_table tr').each(function(index) {
                $(this).find('.row-number').text(index + 1); // تحديث رقم الصف
            });
        }

        $(document).on('click', '.remove-product', function() {
            $(this).closest('tr').remove();
            updateRowNumbers();
        });
    </script>


@endsection
