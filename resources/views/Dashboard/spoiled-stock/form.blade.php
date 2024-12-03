<div class="card-body">
    <div class="row">
        <div class="col-lg-4">
            @include('components.form.select', [
                'collection' => $branches,
                'index' => 'id',
                'select' => isset($data) ? $data->branch_id : old('branch_id'),
                'name' => 'branch_id',
                'label' => trans('admin.branch'),
                'class' => 'form-control select2 branch_id',
                'attribute' => 'required',
                'id' => 'branch_id',
            ])
        </div>
      
        <div class="col-lg-4">
            <label>{{ trans('admin.status') }}</label>
            <select class="form-control select2" id="contactType" name="status">
                <option value="">{{ trans('admin.Select') }}</option>
                <option value="pending">{{ trans('admin.pending') }}</option>
                <option value="final">{{ trans('admin.final') }}</option>
            </select>
        </div>
        <div class="col-lg-12 py-2">
            <div>

                <input type="text" id="search" class="form-control" placeholder="ابحث عن المنتج ...."
                    autocomplete="off" autofocus>
                <ul id="result-list" class="result-list list-group">
                    <!-- Products will be appended here -->
                </ul>

            </div>
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
                        <th>{{ trans('admin.quantity') }}</th>
                        <th>{{ trans('admin.action') }}</th>
                    </tr>
                </thead>
                <tbody class="spoiled_stock_table">
                </tbody>
            </table>
        </div>
    </div>
</div>

@section('script')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <script>
        let rowCounter = 1;
        // Listen to the input event in the search field
        $('#search').on('input', function() {
            let query = $(this).val();
            let branchId = $('#branch_id').val();
            if (!branchId) {
                $('#result-list').empty();
                $('#result-list').append('<li class="list-group-item text-danger">يرجى اختيار فرع أولاً</li>');
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
                            '<li style="cursor: pointer" class="list-group-item">لا توجد منتجات مطابقة</li>');
                    } else {
                        $.each(data, function(index, product) {
                            const isAvailable = product.available_quantity > 0;
                            $('#result-list').append(`
                                <li style="cursor: pointer" class="list-group-item product-item ${isAvailable ? '' : 'disabled'}"
                                    data-id="${product.id}"
                                    data-name="${product.name}"
                                    data-price="${product.unit_price}"
                                    data-available-quantity="${product.available_quantity}"
                                    data-sku="${product.sku}"
                                    ${isAvailable ? '' : 'onclick="return false;"'}>
                                    ${product.name} - SKU: ${product.sku} - Price: ${product.unit_price} - Available: ${product.available_quantity}
                                </li>
                            `);
                        });
                    }
                },
                error: function() {
                    $('#result-list').empty();
                    $('#result-list').append(
                        '<li class="list-group-item text-danger">حدث خطأ أثناء البحث</li>');
                }
            });
        });
        // If change branch
        $('#branch_id').on('change', function() {
            $('#search').val('');
            $('#result-list').empty();
        });
        $(document).on('click', '.product-item', function() {
            let productId = $(this).data('id');
            let branchId = $('#branch_id').val();
            var  productRaw = $('.product_row_' + productId);
          
            if (productRaw.length == 0) {
                $.ajax({
                    url: '{{ route('dashboard.spoiled-stock.ProductRowAdd') }}',
                    method: 'get',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: productId,
                        branch_id: branchId,
                    },
                    success: function(response) {



                        $('.spoiled_stock_table').append(response);


                        $('#search').val('');
                        $('#result-list').empty();

                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        alert('Error: ' + xhr.status + ' ' + xhr.statusText);
                    }
                });
            } else {
       
                var  productQuantityInput = $('.product_row_quantity_' + productId);
                var  quantityAvailable = $('.product_row_available_quantity_' + productId).val();
                var  productQuantity = Number(productQuantityInput.val());
 
                productQuantity++;
                if (productQuantity > quantityAvailable) {
                    alert('لقد تجاوزت الكمية المتاحة');
                    return;
                }
       
                productQuantityInput.val(productQuantity)
            }


        });
        
        $(document).on('click', '.remove_row', function() {
           var tr = $(this).parents().filter(function() {
                return $(this).is('tr');
            });
            tr.remove();
        });

    </script>
@endsection
