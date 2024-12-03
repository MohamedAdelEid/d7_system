<style>
    .traffic-light-toggle {
        display: inline-block;
    }

    .traffic-light-input {
        display: none;
    }

    .traffic-light-label {
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .traffic-light {
        width: 60px;
        height: 26px;
        background-color: #ccc;
        border-radius: 13px;
        position: relative;
        transition: background-color 0.3s;
        margin-right: 10px;
    }

    .traffic-light::before {
        content: '';
        position: absolute;
        width: 22px;
        height: 22px;
        background-color: white;
        border-radius: 50%;
        top: 2px;
        left: 2px;
        transition: transform 0.3s;
    }

    .traffic-light-input:checked+.traffic-light-label .traffic-light {
        background-color: #4CAF50;
    }

    .traffic-light-input:checked+.traffic-light-label .traffic-light::before {
        transform: translateX(34px);
    }

    .label-text {
        font-size: 14px;
    }
</style>

<div class="card-body">
    <div class="row">
        <input type="hidden" name="product_id" value="{{ $data->id }}">
        <div class="col-lg-4">
            <x-form.input type="text" class="form-control" name="name"
                value="{{ isset($data) ? $data->name : old('name') }}" label="{{ trans('admin.name') }}" />
        </div>
        <div class="col-lg-4">
            <x-form.input type="number" class="form-control" name="sku"
                value="{{ isset($data) ? $data->sku : old('sku') }}" label="{{ trans('admin.sku') }}" />
        </div>
        <div class="col-lg-4">
            <x-form.input type="text" class="form-control" name="description"
                value="{{ isset($data) ? $data->description : old('description') }}"
                label="{{ trans('admin.description') }}" />
        </div>

        @if ($settings->display_brands)
            <div class="col-lg-4">
                <x-form.select class="form-control select2" id="" :collection="$brands"
                    select="{{ isset($data) ? $data->brand_id : old('brand_id') }}" index="id" name="brand_id"
                    label="{{ trans('admin.brand') }}" />
            </div>
        @endif
        @if ($settings->display_main_category)
            <div class="col-lg-4">
                <x-form.select class="form-control select2 mainCategoryIdAjax main_category" id="main_category" :collection="$main_categories"
                    select="{{ isset($data) ? $data->main_category_id : old('main_category_id') }}" index="id"
                    name="main_category_id" label="{{ trans('admin.main category') }}" />
            </div>
        @endif
        @if ($settings->display_sub_category)
            <div class="col-lg-4">
                <div class="">
                    <x-form.select class="form-control select2 mainCategoryIdDev sub_category" id="sub_category" :collection="$sub_categories"
                        select="{{ isset($data) ? $data->category_id : old('category_id') }}" index="id"
                        name="category_id" label="{{ trans('admin.sub category') }}" />
                </div>
            </div>
        @endif
        <div class="col-lg-8">
            @php
                $branch_ids = [];
                if (isset($data)) {
                    $branch_ids = $data->Branches()->pluck('branch_id')->toArray();
                }
            @endphp
            <x-form.multiple-select class="form-control select2" id="" :collection="$Branches" :selectArr="$branch_ids"
                index="id" name="branch_ids[]" label="{{ trans('admin.branches') }}" display="name" />
        </div>
        <div class="col-lg-4">
            <div class="form-group mt-4">
                <div class="traffic-light-toggle">
                    @php
                        $productId = isset($data) ? $data->id : 'new';
                        $checkboxId = 'forSale_' . $productId;
                    @endphp
                    <input type="checkbox" id="{{ $checkboxId }}" name="for_sale" value="on"
                        class="traffic-light-input" @checked((isset($data) && $data->for_sale) || old('for_sale') == 'on')>
                    <label for="{{ $checkboxId }}" class="traffic-light-label">
                        <span class="traffic-light"></span>
                        <span class="label-text mr-2 text-bold">{{ trans('admin.for_sale') }}</span>
                    </label>
                </div>
            </div>
        </div>


        <div class="col-lg-4">
            <x-form.input type="number" class="form-control" name="max_sale"
                value="{{ isset($data) ? $data->max_sale : old('max_sale') }}"
                label="{{ trans('admin.max_sale') }}" />
        </div>
        <div class="col-lg-4">
            <x-form.input type="number" class="form-control" name="min_sale"
                value="{{ isset($data) ? $data->min_sale : old('min_sale') }}"
                label="{{ trans('admin.min_sale') }}" />
        </div>
        <div class="col-lg-4">
            <x-form.input type="number" class="form-control" name="quantity_alert"
                value="{{ isset($data) ? $data->quantity_alert : old('quantity_alert') }}"
                label="{{ trans('admin.quantity_alert') }}" />
        </div>

        <div class="col-lg-6">
            <x-form.select class="form-control select2 mainUnitIdAjax main_unit" id="main_unit" :collection="$main_units"
                select="{{ isset($data) ? $data->unit_id : old('unit_id') }}" index="id" name="unit_id"
                label="{{ trans('admin.main unit') }}" display="actual_name" />
        </div>
        @if ($settings->display_sub_units)
            <div class="col-lg-6">
                <label for="sub_unit">{{ trans('admin.sub unit') }}</label>
                <select id="sub_unit" name="sub_unit_ids[]" class="form-control select2 sub_unit" multiple req>
                    <option value="">اختر الوحدة الفرعية</option>
                    @if ($sub_units != null)
                        @foreach ($sub_units as $unit)
                            @if (in_array($unit->id, $data->sub_unit_ids))
                                <option value="{{ $unit->id }}" selected>
                                    {{ $unit->actual_name }}
                                </option>
                            @else
                                <option value="{{ $unit->id }}">
                                    {{ $unit->actual_name }}
                                </option>
                            @endif
                        @endforeach
                    @endif
                </select>
            </div>
        @endif



        <table id="unitTable" class="table col-lg-12 text-center border-1 shadow-md table-hover">
            <thead class="bg-gradient-blue unitTable">
                <tr>
                    <th>الوحدة</th>
                    <th>سعر البيع</th>
                    <th>سعر الشراء</th>
                    @foreach ($salesSegments as $segment)
                        <th>{{ trans('admin.pricefor') }} {{ $segment->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data->ProductUnitDetails as $detail)
                    <tr data-unit-id="{{ $detail->unit->id }}">
                        <td>{{ $detail->unit->actual_name }}</td>
                        <td>
                            <input type="number" class="form-control"
                                name="units[{{ $detail->unit->id }}][sale_price]"
                                value="{{ $data->ProductUnitDetails->where('unit_id', $detail->unit->id)->first()->sale_price ?? '' }}"
                                placeholder="أدخل سعر البيع" required />
                        </td>
                        <td>
                            <input type="number" class="form-control"
                                name="units[{{ $detail->unit->id }}][purchase_price]"
                                value="{{ $data->ProductUnitDetails->where('unit_id', $detail->unit->id)->first()->purchase_price ?? '' }}"
                                placeholder="أدخل سعر الشراء" required/>
                        </td>
                        @foreach ($salesSegments as $segment)
                            <td>
                                <input type="number" class="form-control"
                                    name="units[{{ $detail->unit->id }}][sales_segments][{{ $segment->id }}]"
                                    value="{{ isset($sales_segment_prices[$segment->id][$detail->unit_id]) ? $detail->product->getSalePriceByUnitAndSegment($detail->unit_id, $segment->id) : '' }}"
                                    placeholder="سعر شريحة {{ $segment->name }}" />
                            </td>
                        @endforeach
                    </tr>
                @endforeach

            </tbody>
        </table>

        <!-- Image Upload -->
        <div class="col-lg-12">
            <x-form.file class="form-control" name="image" attribute="" label="{{ trans('admin.image') }}" />
        </div>
    </div>
</div>
