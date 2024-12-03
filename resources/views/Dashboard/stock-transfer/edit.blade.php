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
            pointer-events: none; /* Prevent any clicks */
            opacity: 0.5; /* Make it look disabled */
            background-color: #f8f9fa; /* Change background color if needed */
        }
        .result-list li:hover {
            background-color: #ddd;
        }
    </style>
@endsection

@section('title', trans('admin.stock_transfers'))
@section('content')


<style>
    .modal-dialog{
        max-width: 1000px;
    }
</style>
        <!-- form start -->
    <form method="post" action="{{route('dashboard.stock-transfers.update', $stock_transfer->id)}}">
            @csrf
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ trans('admin.stock_transfers') }}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('dashboard.home')}}">{{ trans('admin.Home') }}</a> / <a href="{{route('dashboard.stock-transfers.index')}}">{{ trans('admin.stock_transfers') }}</a> / {{ trans('admin.Edit') }}</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
               <div class="row my-2">
                   <!-- From Branch -->
                    <div class="col-lg-3">
                        @include('components.form.select', [
                            'collection' => $branches,
                            'index' => 'id',
                            'select' => isset($data) ? $data->from_branch_id : auth()->user()->branch_id, // Pre-select from_branch_id
                            'name' => 'from_branch_id',
                            'label' => trans('admin.from_branch'),
                            'class' => 'form-control select2 from_branch_id branch-select',
                            'attribute' => 'required',
                        ])
                    </div>

                    <!-- To Branch -->
                    <div class="col-lg-3">
                        @include('components.form.select', [
                            'collection' => $branches,
                            'index' => 'id',
                            'select' => isset($to_branch_id ) ? $to_branch_id : old('to_branch_id'), // Pre-select to_branch_id
                            'name' => 'to_branch_id',
                            'label' => trans('admin.to_branch'),
                            'class' => 'form-control select2 to_branch_id branch-select',
                            'attribute' => 'required',
                        ])
                    </div>

                    <!-- Status -->
                    <div class="col-lg-3">
                        <label>{{ trans('admin.status') }}</label>
                        <select class="form-control select2" id="contactType" name="status">
                            <option value="">{{ trans('admin.Select') }}</option>
                            <option value="pending" {{ (isset($stock_transfer) && $stock_transfer->status == 'pending') ? 'selected' : '' }}>
                                {{ trans('admin.pending') }}
                            </option>
                            <option value="final" {{ (isset($stock_transfer) && $stock_transfer->status == 'final') ? 'selected' : '' }}>
                                {{ trans('admin.final') }}
                            </option>
                        </select>
                    </div>
                    <div class="col-sm-3 d-flex align-items-center justify-content-end">
                
                        <button type="button" class="btn btn-success fire-popup ml-2"
                        data-toggle="modal"
                        data-target="#getByBrand"
                        >{{ trans('admin.Add Bulck products') }}</button>
            
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
                            
                                <input type="text" id="search" class="form-control"  placeholder="ابحث عن المنتج ...." autocomplete="off" autofocus>
                                <ul id="result-list" class="result-list list-group">
                                    <!-- Products will be appended here -->
                                </ul>
                            
                            </div>
                        </div>

                    
                        <div class="col-lg-12 my-3">
                            @if($errors->any())
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
                            <button type="submit" class="btn btn-success">{{ trans('admin.Edit') }}</button>
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

<script>
    $(document).ready(function() {
        let existingProducts = @json($existingProducts);

        let rowCounter = 1;

        existingProducts.forEach(function(product) {
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
                        <input type="number" class="form-control quantity" 
                            name="products[${product.id}][quantity]" 
                            value="${product.quantity}"  min="1" max="${product.available_quantity + product.quantity }">
                    </td>
                    <td class="available-quantity">${product.available_quantity}</td>
                    <td><button type="button" class="btn btn-danger remove-product">حذف</button></td>
                    <td>
                        <input type="hidden" name="products[${product.id}][product_id]" value="${product.id}">
                        <input type="hidden" name="products[${product.id}][id]" value="${product.id}">
                        <input type="hidden" class="main_available_quantity_${product.id}" name="products[${product.id}][main_available_quantity]" value="${product.available_quantity}">
                        <input type="hidden" class="unit_multipler_${product.id}" name="products[${product.id}][unit_multipler]" value="0">
                    </td>
                </tr>
            `);
        });
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
                    $('#result-list').empty();
                    $('#result-list').append('<li class="list-group-item text-danger">يرجى اختيار فرع أولاً</li>');
                    return;
                }
                $.ajax({
                    url: '{{ route("dashboard.sells.products.search") }}',
                    method: 'GET',
                    data: {
                        query: query,
                        branch_id: branchId
                    },
                    success: function(data) {
                        $('#result-list').empty();
                        if (data.length === 0) {
                            $('#result-list').append('<li class="list-group-item">لا توجد منتجات مطابقة</li>');
                        } else {
                            $.each(data, function(index, product) {
                                const isAvailable = product.available_quantity > 0;
                                $('#result-list').append(`
                                    <li class="list-group-item product-item ${isAvailable ? '' : 'disabled'}"
                                        data-id="${product.id}"
                                        data-name="${product.name}"
                                        data-available-quantity="${product.available_quantity}"
                                        data-sku="${product.sku}"
                                        ${isAvailable ? '' : 'onclick="return false;"'}>
                                        ${product.name} - SKU: ${product.sku}  - Available: ${product.available_quantity}
                                    </li>
                                `);
                            });
                        }
                    },
                    error: function() {
                        $('#result-list').empty();
                        $('#result-list').append('<li class="list-group-item text-danger">حدث خطأ أثناء البحث</li>');
                    }
                });
            });
            // If change branch
            $('.from_branch_id').on('change', function() {
                $('#search').val('');
                $('#result-list').empty();
                $('.sell_table_AddBulckProducts').empty();
            });
            // Add Products
            $(document).on('click', '.product-item', function() {
                let productId = $(this).data('id');
                let branchId = $('.from_branch_id').val();
        
             
                    $.ajax({
                        url: '{{ route("dashboard.sells.products.row.add") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            product_id: productId,
                            branch_id: branchId,
                        },
                        success: function(response) {
                            let existingRow = $(`.sell_table tr[data-product-id="${response.id}"]`);
                            if (existingRow.length > 0) {
                                let quantityInput = existingRow.find('.quantity');
                                let currentQuantity = parseInt(quantityInput.val()) || 0;
                                let newQuantity = currentQuantity + 1;
                                quantityInput.val(newQuantity);
                                existingRow.find('.available-quantity').text(response.available_quantity - newQuantity);
                            } else {
                            
                                $('.sell_table').append(`
                                    <tr data-product-id="${response.id}">
                                        <td>${rowCounter++}</td>
                                        <td>${response.name}</td>
                                        <td>
                                            <select class="form-control unit-select" name="products[${response.id}][unit_id]">
                                                ${response.units.map(unit => `<option value="${unit.id}" data-multipler="${unit.multipler}" ${response.unit == unit.id ? 'selected' : ''}>${unit.actual_name}</option>`).join('')}
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control quantity" 
                                                name="products[${response.id}][quantity]" 
                                                value="1" min="1" max="${response.available_quantity + response.quantity}" required>
                                            <small class="error-message" style="color: red; display: none;"></small> <!-- Message area -->
                                        </td>
                                        <td class="available-quantity">${response.available_quantity}</td>
                                        <td><button type="button" class="btn btn-danger remove-product">حذف</button></td>
                                        <input type="hidden" name="products[${response.id}][product_id]" value="${response.id}">
                                        <input type="hidden" name="products[${response.id}][id]" value="${response.id}">
                                        <input type="hidden" class="main_available_quantity_${response.id}" name="products[${response.id}][main_available_quantity]" value="${response.available_quantity}">
                                        <input type="hidden" class="unit_multipler_${response.id}" name="products[${response.id}][unit_multipler]" value="0">
                                    </tr>
                                `);
                                
                                scrollToBottom(); // Scroll to the last added row
                            }
            
                            $('#search').val('');
                            $('#result-list').empty();
            
                            
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                            alert('Error: ' + xhr.status + ' ' + xhr.statusText);
                        }
                    });
               
        
            });
        // Change Unit
        $(document).on('change', '.unit-select', function() {
            var $row = $(this).closest('tr'); // تحديد الصف الحالي
            var product_row_id = $row.data('product-id'); // جلب ID الصف الحالي
            var selectedUnitId = $(this).val(); // جلب ID الوحدة المختارة
            var branchId = $('.from_branch_id').val(); // جلب ID الفرع إذا كان ضروريًا
    
            $.ajax({
                url: '{{ route("dashboard.units.product.update") }}', // مسار تحديث الوحدة
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: product_row_id,
                    unit_id: selectedUnitId,
                    branch_id: branchId // أضف المعلمات الأخرى حسب الحاجة
                },
                success: function(response) {
                    // تأكد من أن الاستجابة تحتوي على السعر والمعلومات الأخرى
                    if (response.success) {
                        // تحديث السعر ومعلومات أخرى
                        var quantityInput = $row.find('.quantity'); // جلب حقل الكمية في الصف الحالي
                        var quantity = parseInt(quantityInput.val()) || 0;
                  
                        // تحديث الحقل المخفي لمضاعف الوحدة في الصف الحالي
                        $row.find('.unit_multipler_' + product_row_id).val(response.unit_multipler);
    
                        // تحديث الكمية المتاحة
                        var availableQuantity = response.available_quantity;
    
    
                        $row.find('.available-quantity').text(availableQuantity);
    
                        // حساب الإجمالي النهائي بعد التحديث
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
  
        //Quantity
        $(document).on('input', '.quantity', function() {
            let row = $(this).closest('tr');
            let quantity = parseInt($(this).val()) || 0;
            let currentQuantity = parseInt(quantity.val()) || 0;
            let newQuantity = currentQuantity + 1;
                        
        });
        // Remove
        $(document).on('click', '.remove-product', function() {
            $(this).closest('tr').remove();
            calculateFinalTotal();
            updateRowNumbers(); // Update row numbers after removal
        });
     
     
     
    
        function updateRowNumbers() {
            let counter = 1;
            $('.sell_table tr').each(function() {
                $(this).find('td:first').text(counter++);
            });
            calculateFinalTotal();
        }
        
        function scrollToBottom() {
            $("html, body").animate({ scrollTop: $(document).height() }, 1000);
        }
    
    
        });
    
        $(document).ready(function() {
            // عندما يتغير البراند، قم بجلب المنتجات المتناسبة
            $('#brand_id').on('change', function() {
                let brandId = $(this).val();
                let branchId = $('.from_branch_id').val();
             
                if(!branchId){
                $('.sell_table_AddBulckProducts').empty();
                $('.sell_table_AddBulckProducts').append('<tr><td colspan="5" class="text-center text-danger"> يجب اختيار الفرع اولا</td></tr>');
                }else{
                // مسح الجدول قبل تحميل منتجات جديدة
                $('.sell_table_AddBulckProducts').empty();
                // جلب المنتجات للبراند المحدد
                $.ajax({
                    url: '{{ route("dashboard.sells.products.getByBrand") }}', // تعديل هذا المسار حسب الحاجة
                    method: 'GET',
                    data: {
                        brand_id: brandId,
                        branch_id: branchId
                    },
                    success: function(products) {
                        console.log(products); 
                        
                       
                            if (products.length === 0) {
                                $('.sell_table_AddBulckProducts').append('<tr><td colspan="5" class="text-center">لا توجد منتجات لهذا البرند</td></tr>');
                            } else {
                            
                                // إضافة كل منتج إلى الجدول
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
                                                    value="1" min="1" max="${product.available_quantity + product.quantity}" required>
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
                        $('.sell_table_AddBulckProducts').append('<tr><td colspan="7" class="text-center text-danger">حدث خطأ أثناء تحميل المنتجات</td></tr>');
                    }
                });
                }
    
            
                
            });
 
    
            // إضافة المنتج إلى الجدول الرئيسي
            $('.modal-footer .btn-primary').on('click', function() {
                let totalToAdd = 0; // متغير لتتبع المجموع المضاف
                $('.sell_table_AddBulckProducts tr').each(function() {
                    let row = $(this);
                    let productId = row.data('product-id');
                    let quantity = parseInt(row.find('.quantity').val()) || 0; // استخدام parseInt لضمان الحصول على عدد صحيح
                    let max = parseInt(row.find('.quantity').attr('max')) ;
                    // إضافة فقط إذا كانت الكمية أكبر من الصفر
                    if (quantity > 0) {
                        // التحقق مما إذا كان المنتج موجودًا بالفعل في الجدول الرئيسي
                        let existingRow = $('.sell_table tr[data-product-id="' + productId + '"]');
                        if (existingRow.length > 0) {
                            // إذا كان المنتج موجودًا، تحديث الكمية والإجمالي
                            let existingQuantity = parseInt(existingRow.find('.quantity').val()) || 0;
                            let newQuantity = existingQuantity + quantity;
    
                            existingRow.find('.quantity').val(newQuantity);
                        } else {
                            // إذا لم يكن المنتج موجودًا، إضافته إلى الجدول الرئيسي
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
                $(this).closest('tr').remove(); // إزالة الصف
                updateRowNumbers(); // تحديث أرقام الصفوف
            });
            $('.contact_id').on('change', function() {
                $('.sell_table').empty();
                $('.sell_table_AddBulckProducts').empty();
                calculateFinalTotal();
            });
    
        
        });
    
    </script>
    

@endsection
