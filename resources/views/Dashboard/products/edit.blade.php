@extends('layouts.admin')

@section('title', trans('admin.products'))

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('admin.Edit') }} {{ trans('admin.products') }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">{{ trans('admin.Home') }}</a> / <a
                                href="{{ route('dashboard.products.index') }}">{{ trans('admin.products') }}</a> /
                            {{ trans('admin.Edit') }}</li>
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
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-danger m-2" role="alert">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" action="{{ route('dashboard.products.update', $data->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @include('Dashboard.products.edit-form')

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ trans('admin.Save') }}</button>
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
    // عند تغيير الوحدة الأساسية (main_unit)
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

                    $('#sub_unit').append('<option value="">اختر الوحدة الفرعية</option>');

                    $.each(response, function(index, unit) {
                        var isSelected = $('#sub_unit_ids').val() && $('#sub_unit_ids').val().includes(unit.id.toString());
                        $('#sub_unit').append('<option value="' + unit.id + '" ' + (isSelected ? 'selected' : '') + '>' + unit.actual_name + '</option>');
                    });
                }
            });
        } else {
            $('#sub_unit').empty();
        }
    });

    var baseUnitId = $('#main_unit').val();
    if (baseUnitId) {
        $.ajax({
            url: "{{ route('dashboard.units.subUnits') }}",
            type: 'GET',
            data: {
                base_unit_id: baseUnitId
            },
            success: function(response) {
                $('#sub_unit').empty();
                $('#sub_unit').append('<option value="">اختر الوحدة الفرعية</option>');
                $.each(response, function(index, unit) {
                    var isSelected = $('#sub_unit_ids').val() && $('#sub_unit_ids').val().includes(unit.id.toString());
                    $('#sub_unit').append('<option value="' + unit.id + '" ' +  'selected' + '>' + unit.actual_name + '</option>');
                });
            }
        });
    }
});

    </script>
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
                                '<option value="">اختر الفئة الفرعية</option>');

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
                    $('#sub_category').append('<option value="">اختر الفئة الفرعية</option>');
                }
            });
        });
    </script>
<script>
    $(document).on('change', '#main_unit', function() {
        // عند تغيير الوحدة الرئيسية، يتم تفريغ الجدول وإلغاء تحديد الوحدات الفرعية
        $('#sub_unit').val(''); // إلغاء تحديد الوحدات الفرعية
        $('#unitTable tbody').empty(); // تفريغ الجدول
        $('#unitTable').hide(); // إخفاء الجدول إذا كان فارغًا
    });

    $(document).on('change', '#sub_unit, #main_unit', function() {
        let selectedUnitIds = [];
        $('#sub_unit option:selected, #main_unit option:selected').each(function() {
            selectedUnitIds.push($(this).val());
        });

        if (selectedUnitIds.length === 0) {
            $('#unitTable tbody').empty();
            $('#unitTable').hide();
            return;
        }

        $('#unitTable').show();

        let existingUnitIds = [];
        $('#unitTable tbody tr').each(function() {
            existingUnitIds.push($(this).data('unit-id').toString());
        });

        existingUnitIds.forEach(unitId => {
            if (!selectedUnitIds.includes(unitId)) {
                $('#unitTable tbody tr[data-unit-id="' + unitId + '"]').remove();
            }
        });

        let salesSegments = @json($salesSegments);
        selectedUnitIds.forEach(unitId => {
            if (!existingUnitIds.includes(unitId)) {
                let unitText = $('#sub_unit option[value="' + unitId + '"], #main_unit option[value="' +
                    unitId + '"]').text();

                if (unitId) {
                    let row = `
            <tr data-unit-id="${unitId}">
                <td>${unitText}</td>
                <td>
                    <input type="text" class="form-control" name="units[${unitId}][sale_price]"
                           placeholder="أدخل سعر البيع" step="0.1" />
                </td>
                <td>
                    <input type="text" class="form-control" name="units[${unitId}][purchase_price]"
                           placeholder="أدخل سعر الشراء" step="0.1" />
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
            }
        });
    });
</script>


@endsection
