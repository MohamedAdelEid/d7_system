@extends('layouts.blank')

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

@section('title', trans('admin.drafts'))
@section('content')
@php
    $is_edit = false;
    if(isset($sell))
        $is_edit = true;

    $disabled = '';
    if($is_edit)
        $disabled= 'disabled';
    $product_segments = [];
    // Log::info($sell);
    if(isset($sell) && $sell->contact->salesSegment) {
        $product_segments = $sell->contact->salesSegment->products()->pluck('products.id');
    }
@endphp

<style>
    .modal-dialog{
        max-width: 1000px;
    }
</style>
     <!-- form start -->
<form method="post" action="{{route('dashboard.sells.draft.finish', $sell->id)}}">
        @csrf
        
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-1">
            <div class="col-sm-12 d-flex align-items-center justify-content-end">
          
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('dashboard.home')}}">{{ trans('admin.Home') }}</a> / <a href="{{route('dashboard.sells.drafts.index')}}">{{ trans('admin.drafts') }}</a> / {{ trans('admin.Create') }}</li>
                </ol>
                </div><!-- /.col -->
        </div>
        <div class="row mb-2">
            <div class="col-sm-4">
                <div class="col-lg-12">
                    @include('components.form.select', [
                        'collection' => $branches,
                        'id' => 'branch_id',
                        'index' => 'id',
                        'select' => isset($sell) ? $sell->branch_id : auth()->user()->branch_id,
                        'name' => 'branch_id',
                        'label' => trans('admin.branch').":",
                        'class' => 'form-control select2 branch_id w-100',
                        'attribute' => 'required ' . $disabled,
                    ])
                </div>
            </div><!-- /.col -->
            <div class="col-sm-4">
                <div class="col-lg-12">
                    @include('components.form.select', [
                        'collection' => $contacts,
                        'index' => 'id',
                        'select' => isset($sell) ? $sell->contact_id : $cash_contact?->id,
                        'name' => 'contact_id',
                        'label' => trans('admin.contact'),
                        'class' => 'form-control select2 contact_id',
                        'attribute' => 'required ' . $disabled,
                    ])
                </div>
            </div><!-- /.col -->
            <div class="col-sm-4 d-flex align-items-center justify-content-end">
          
                <button type="button" class="btn btn-success fire-popup ml-2"
                data-toggle="modal"
                data-target="#getByBrand"
                >{{ trans('admin.Add Bulck products') }}</button>

            </div><!-- /.col -->
        </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content ">
        <div id="print-section">
        </div>
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
                          
                            <input type="text" id="search" class="form-control" placeholder="ابحث عن المنتج ...." autocomplete="off" autofocus>
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
                                <option value="percentage" {{ isset($sell) && $sell->discount_type == 'percentage' ? 'selected' : '' }}>{{ trans('admin.percentage') }}</option>
                                <option value="fixed_price" {{ isset($sell) && $sell->discount_type == 'fixed_price' ? 'selected' : '' }}>{{ trans('admin.fixed amount') }}</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="discount_value">{{ trans('admin.amount') }}</label>
                            <input type="number" class="form-control" id="discount_value" name="discount_value" value="{{isset($sell)  ? $sell->discount_value : '0'}}" min="0" step="1" required>
                        </div>
                    </div>
                    
                    <div>
                        <input type="hidden" name="final_price" id="final_price" value="0.00"> <!-- حقل مخفي لقيمة final_price -->

                        <h5>الإجمالي النهائي: <span class="final_total">{{isset($sell) ? $sell->final_price : '0.00'}}</span></h5>
                        
                    </div>
                </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success button-send" name="sell_type"
                        value="cash">{{ trans('admin.cash') }}</button>
                        <button type="submit" class="btn btn-primary button-send credit_button"
                         name="sell_type"
                        value="credit">{{ trans('admin.credit') }}</button>
                        <button type="button" class="btn btn-primary fire-popup" data-toggle="modal"
                        data-target="#modal-default"
                        data-url="{{ route('dashboard.sells.multiPay') }}">{{ trans('admin.multi_pay') }}</button>
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


@php

    $existingProducts = $sell->TransactionSellLines->map(function ($line) use ($SellService, $sell) {
        $product = $line->Product;


        return [
            'id' => $product->id,
            'name' => $product->name,
            'unit' => $product->MainUnit->id,
            'units' => $product->GetAllUnits(),
            'available_quantity' => $product->getStockByBranch($sell->branch_id),
            'unit_price' => $line->unit_price,
            'quantity' => $line->quantity, 
            'max_sale' => $product->max_sale, 
            'min_sale' => $product->min_sale, 
        ];
    });

@endphp

<script>
    $(document).ready(function() {
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
        let rowCounter = 1; 

        let existingProducts = @json($existingProducts); 
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
                            value="${product.quantity}"  min="${product.available_quantity > product.min_sale ? product.min_sale  : product.available_quantity}" max="${ product.available_quantity > product.max_sale ? product.max_sale : product.available_quantity}" >
                    </td>
                    <td class="available-quantity">${product.available_quantity}</td>
                    <td>
                        <input type="number" class="form-control unit-price" 
                            name="products[${product.id}][unit_price]" 
                            value="${product.unit_price}" min="0" step="1">
                    </td>
                    <td class="total">${(product.quantity * product.unit_price).toFixed(2)}</td> <!-- حساب المجموع -->
                    <td><button type="button" class="btn btn-danger remove-product">حذف</button></td>

                    <input type="hidden" name="products[${product.id}][product_id]" value="${product.id}">
                    <input type="hidden" name="products[${product.id}][id]" value="${product.id}">
                    <input type="hidden" class="main_unit_price_${product.id}" name="products[${product.id}][main_unit_price]" value="${product.unit_price}">
                    <input type="hidden" class="main_available_quantity_${product.id}" name="products[${product.id}][main_available_quantity]" value="${product.available_quantity}">
                    <input type="hidden" class="unit_multipler_${product.id}" name="products[${product.id}][unit_multipler]" value="0">
                </tr>
            `);
        });
    });
    scrollToBottom(); // Scroll to the last added row
    calculateFinalTotal() 
</script>


@include('Dashboard.includes.product_row_ajax')


@endsection
