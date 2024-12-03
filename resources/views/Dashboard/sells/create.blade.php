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
 
    </style>
@endsection

@section('title', trans('admin.sells'))
@section('printsection')
    <div id="print-section">

    </div>
@endsection
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
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-1">
                    <div class="col-sm-12 d-flex align-items-center justify-content-end">

                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">{{ trans('admin.Home') }}</a>
                                / <a href="{{ route('dashboard.sells.index') }}">{{ trans('admin.sells') }}</a> /
                                {{ trans('admin.Create') }}</li>
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
                                'label' => trans('admin.branch') . ':',
                                'class' => 'form-control select2 branch_id w-100',
                                'attribute' => 'required ' . $disabled,
                            ])
                        </div>
                    </div><!-- /.col -->
                    <div class="col-sm-4">
               
                        <label for="contact_id">{{ trans('admin.contact') }}</label>
                        <select name="contact_id" id="contact_id" class="form-control select2 contact_id" required>
                            <option value="" selected >{{ trans('admin.Select') }}</option>
                            @foreach ($contacts as $contact)
                            <option value="{{$contact->id}}" @if ($contact->credit_limit == 0) selected @endif>{{$contact->name}} - {{$contact->balance}}</option>

                            @endforeach
                        </select>
                    </div><!-- /.col -->

                    <div class="col-sm-4 d-flex align-items-center justify-content-end">

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
                                    @if ($errors->any())
                                        <div class="alert alert-danger m-2" role="alert">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    <table class="table table-bordered table-striped ">
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
                                    <h5>الإجمالي النهائي: <span class="final_total">0.00</span></h5>
                                    <input type="hidden" name="final_total" id="final_total">
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-success button-send" name="sell_type"
                                    value="cash">{{ trans('admin.cash') }}</button>
                                <button type="submit" class="btn btn-info button-send credit_button" style="display: none;"
                                    name="sell_type" value="credit">{{ trans('admin.credit') }}</button>
                                <button type="button" class="btn btn-primary fire-popup" data-toggle="modal"
                                    data-target="#modal-default"
                                    data-url="{{ route('dashboard.sells.multiPay') }}">{{ trans('admin.multi_pay') }}</button>
                                <button type="submit" class="btn btn-dark draft" name="sell_type"
                                    value="draft">{{ trans('admin.draft') }}</button>
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
    // Initialize Select2 and focus on search when dropdown opens
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "{{ trans('admin.Select') }}", // Placeholder for the dropdown
        width: 'resolve' // Make sure it takes full width as specified
    }).on('select2:open', function(e) {
        // Focus on search box within Select2 when dropdown is opened
        let searchField = document.querySelector('.select2-container--open .select2-search__field');
        if (searchField) {
            searchField.focus();
        }
    });
});

</script>
    {{-- X-CSRF-TOKEN --}}

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    @include('Dashboard.includes.product_row_ajax')
    {{-- old for printing invoice debend on session --}}
    {{-- <script>
        @if (Session::has('transaction'))
            let printUrl =
                @if (session('classic_printing'))
                    "{{ route('dashboard.sells.printInvoicePage', Session::get('transaction')->id) }}"
                @else
                    "{{ route('dashboard.sells.printThermalInvoice', Session::get('transaction')->id) }}"
                @endif ;

            $.ajax({
                url: printUrl,
                type: 'get',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#print-section').html(response);
                    setTimeout(() => {
                        window.print();
                    }, 1000);

                    console.log("AJAX request successful:", response);
                },
                error: function(xhr, status, error) {
                    console.log("AJAX request failed:", error);
                }
            });
        @endif
    </script> --}}

    {{-- create transaction sell and print it  debend on ajax request  --}}
    <script>
        $(document).ready(function() {

            $(document).on('click', '.button-send', function(e) {
                e.preventDefault();

                data = {};

                data._token = "{{ csrf_token() }}";
                data.branch_id = $('#branch_id').val();
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
                data.final_total = $('#final_total').val();
                data.sell_type = $(this).val();


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

                $(this).prop('disabled', true);
                $.ajax({
                    url: "{{ route('dashboard.sells.store') }}",
                    type: 'POST',
                    data: data,

                    success: function(response) {
                        console.log('================================');
                        console.log(response);
                        $('.sell_table').empty();
                        var transactionId = response.transaction.id
                        var printUrl = "{{ route('dashboard.sells.printInvoicePage', ':id') }}"
                            .replace(':id', transactionId);
                        console.log(printUrl);
                        $.ajax({
                            url: printUrl,
                            type: 'get',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                $('#print-section').html(response);
                                setTimeout(() => {
                                    window.print();
                                }, 1000);

                                console.log("AJAX request successful:", response);
                            },
                            error: function(xhr, status, error) {
                                console.log("AJAX request failed:", error);
                            }
                        });
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
                // $('#contact_id').val('');
                $('.final_total').text('0.00');
            })
            $('.my-popup').on('hidden.bs.modal', function() {
                $('.my-popup .modal-title').empty();
                $('.my-popup .modal-body').empty(); // $(this).empty();
            });

            $(document).on("keydown", function(event) {

                if (event.key === "Enter") {
                    event.preventDefault();

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
