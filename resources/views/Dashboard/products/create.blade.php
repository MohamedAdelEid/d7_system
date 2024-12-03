@extends('layouts.admin')

@section('title', trans('admin.products'))

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('admin.products') }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">{{ trans('admin.Home') }}</a> / <a
                                href="{{ route('dashboard.products.index') }}">{{ trans('admin.products') }}</a> /
                            {{ trans('admin.Create') }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{ trans('admin.Create') }}</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" action="" enctype="multipart/form-data">
                            @csrf
                            @include('Dashboard.products.form')
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" name="action" value="add_and_open_stock"
                                    class="btn btn-primary">{{ trans('admin.add_and_open_stock') }}</button>
                                <button type="submit" name="action" value="add"
                                    class="btn btn-primary">{{ trans('admin.Add') }}</button>
                            </div>

                        </form>
                    </div>
                    <!-- /.card -->
                </div>
            </div><!-- /.container-fluid -->
    </section>
@endsection


@section('script')
    <script>
        $(document).ready(function() {
            $('#main_category').on('change', function() {
                var mainCategoryId = $(this).val(); 

                if (mainCategoryId) {
                    $.ajax({
                        url: "{{ route('dashboard.categories.subCategoriesAjax') }}", 
                        type: 'GET',
                        data: {
                            category_id: mainCategoryId
                        }, 
                        success: function(response) {
                            $('#sub_category').empty();
                            $('#sub_category').append(
                                '<option value="">اختر الوحدة الفرعية</option>');

                            $.each(response, function(index, category) {
                                $('#sub_category').append(
                                    '<option value="' + category.id + '">' +
                                    category.name + '</option>'
                                );
                            });
                        },
                        error: function(xhr) {
                            console.error('Error fetching sub-categories:', xhr.responseText);
                        },
                    });
                } else {
                    $('#sub_category').empty();
                    $('#sub_category').append('<option value="">اختر الوحدة الفرعية</option>');
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#main_unit').change(function() {
                var baseUnitId = $(this).val();

                if (baseUnitId) {
                    $.ajax({
                        url: "{{ route('dashboard.units.subUnits') }}",
                        type: 'GET',
                        data: {
                            base_unit_id: baseUnitId
                        },
                        success: function(response) {
                            $('#sub_unit').empty();
                            $.each(response, function(index, unit) {
                                $('#sub_unit').append('<option value="' + unit.id +
                                    '">' + unit.actual_name + '</option>');
                            });
                        }
                    });
                } else {
                    $('#sub_unit').empty();
                }
            });

            // تحديث الجدول عند تغيير الوحدة الرئيسية أو الوحدات الفرعية
            $(document).on('change', '#sub_unit, #main_unit', function() {
                $('#unitTable tbody').empty();

                // Hide the table if no unit is selected
                if ($('#sub_unit option:selected').val() === null && $('#main_unit option:selected')
                    .val() === null) {
                    $('#unitTable').hide();
                    return;
                }

                // Show the table when a unit is selected
                $('#unitTable').show();

                let selectedUnits = $('#sub_unit option:selected, #main_unit option:selected');
                let salesSegments =
                    @json($salesSegments); // Pass sales segments from the server-side

                selectedUnits.each(function() {
                    let unitId = $(this).val();
                    let unitText = $(this).text();

                    if (unitId) {
                        let row = `
            <tr data-unit-id="${unitId}">
                <td>${unitText}</td>
                <td>
                    <input type="text" class="form-control" name="units[${unitId}][sale_price]" 
                           placeholder="أدخل سعر البيع" step="0.1" required />
                </td>
                <td>
                    <input type="text" class="form-control" name="units[${unitId}][purchase_price]" 
                           placeholder="أدخل سعر الشراء" step="0.1" required />
                </td>`;

                        salesSegments.forEach(segment => {
                            row += `
                <td>
                    <input type="number" class="form-control" 
                           name="units[${unitId}][sales_segments][${segment.id}]" 
                           placeholder="سعر شريحة ${segment.name}" step="0.1" />
                </td>`;
                        });

                        row += `</tr>`;
                        $('#unitTable tbody').append(row);
                    }
                });
            });

        });
    </script>


@endsection
