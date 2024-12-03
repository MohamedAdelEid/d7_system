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
@if ($errors->any())
<div class="alert alert-danger m-2" role="alert">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
@foreach ($products as $data)
    <div class="card-header h5 bg-dark text-light"> {{ $data->name }}</div>
    <div class="card-body shadow mb-5 pb-5 border">
        <input type="hidden" name="bulk_edit_ids[]" value="{{ $data->id }}">
            <div class="row">
                <input type="hidden" name="products[{{ $data->id }}][product_id]" value="{{ $data->id }}">
                <div class="col-lg-4">
                    <x-form.input type="text" class="form-control" name="products[{{ $data->id }}][name]"
                        value="{{ isset($data) ? $data->name : old('name') }}" label="{{ trans('admin.name') }}" />
                </div>
                <div class="col-lg-4">
                    <x-form.input type="text" class="form-control" name="products[{{ $data->id }}][sku]"
                        value="{{ isset($data) ? $data->sku : old('sku') }}" label="{{ trans('admin.sku') }}" />
                </div>
                <div class="col-lg-4">
                    <x-form.input type="text" class="form-control" name="products[{{ $data->id }}][description]"
                        value="{{ isset($data) ? $data->description : old('description') }}"
                        label="{{ trans('admin.description') }}" />
                </div>

                @if ($settings->display_brands)
                    <div class="col-lg-4">
                        <x-form.select class="form-control select2" id="" :collection="$brands"
                            select="{{ isset($data) ? $data->brand_id : old('brand_id') }}" index="id"
                            name="products[{{ $data->id }}][brand_id]" label="{{ trans('admin.brand') }}" />
                    </div>
                @endif
                @if ($settings->display_main_category)
                    <div class="col-lg-4">
                        <x-form.select class="form-control select2 mainCategoryIdAjax" id="" :collection="$main_categories"
                            select="{{ isset($data) ? $data->main_category_id : old('main_category_id') }}"
                            index="id" name="products[{{ $data->id }}][main_category_id]" label="{{ trans('admin.main category') }}" />
                    </div>
                @endif
                @if ($settings->display_sub_category)
                    <div class="col-lg-4">
                        <div class="">
                            <x-form.select class="form-control select2 mainCategoryIdDev" id=""
                                :collection="$sub_categories" select="{{ isset($data) ? $data->category_id : old('category_id') }}"
                                index="id" name="products[{{ $data->id }}][category_id]" label="{{ trans('admin.sub category') }}" />
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
                    <x-form.multiple-select class="form-control select2" id="" :collection="$Branches"
                        :selectArr="$branch_ids" index="id" name="products[{{ $data->id }}][branch_ids][]" label="{{ trans('admin.branches') }}"
                        display="name" />
                </div>
                <div class="col-lg-4">
                    <div class="form-group mt-4">
                        <div class="traffic-light-toggle">
                            @php
                                $productId = isset($data) ? $data->id : 'new';
                                $checkboxId = 'forSale_' . $productId;
                            @endphp
                            <input type="checkbox" id="{{ $checkboxId }}" name="products[{{ $data->id }}][for_sale]" value="on"
                                class="traffic-light-input" @checked((isset($data) && $data->for_sale) || old('for_sale') == 'on')>
                            <label for="{{ $checkboxId }}" class="traffic-light-label">
                                <span class="traffic-light"></span>
                                <span class="label-text mr-2 text-bold">{{ trans('admin.for_sale') }}</span>
                            </label>
                        </div>
                    </div>
                </div>


                <div class="col-lg-4">
                    <x-form.input type="number" class="form-control" name="products[{{ $data->id }}][max_sale]"
                        value="{{ isset($data) ? $data->max_sale : old('max_sale') }}"
                        label="{{ trans('admin.max_sale') }}" />
                </div>
                <div class="col-lg-4">
                    <x-form.input type="number" class="form-control" name="products[{{ $data->id }}][min_sale]"
                        value="{{ isset($data) ? $data->min_sale : old('min_sale') }}"
                        label="{{ trans('admin.min_sale') }}" />
                </div>
                <div class="col-lg-4">
                    <x-form.input type="number" class="form-control" name="products[{{ $data->id }}][quantity_alert]"
                        value="{{ isset($data) ? $data->quantity_alert : old('quantity_alert') }}"
                        label="{{ trans('admin.quantity_alert') }}" />
                </div>

                <!-- Table for Unit Prices and Sales Segments -->
                <table id="unitTable" class="table col-lg-12 text-center border-1 shadow-md table-hover ">
                    <thead class="bg-gradient-blue">
                        <tr>
                            <th>الفئة</th>
                            <th>سعر البيع</th>
                            <th>سعر الشراء</th>
                            @foreach ($salesSegments as $segment)
                                <th>{{ trans('admin.pricefor') }} {{ $segment->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data->ProductUnitDetails as $item)
                            @php
                                $unit = App\Models\Unit::find($item->unit_id);
                            @endphp
                            <tr>
                                <td>{{ $unit->actual_name }}</td>
                                <td>
                                    <input type="number" class="form-control" step="0.1"
                                        name="products[{{ $data->id }}][units][{{ $item->unit_id }}][sale_price]"
                                        value="{{ isset($item->sale_price) ? number_format($item->sale_price, 2) : old('unit_price') }}"
                                        placeholder="أدخل سعر البيع" />
                                </td>
                                <td>
                                    <input type="number" class="form-control" step="0.1"
                                        name="products[{{ $data->id }}][units][{{ $item->unit_id }}][purchase_price]"
                                        value="{{ isset($item->purchase_price) ?  number_format($item->purchase_price, 2)  : old('unit_price') }}"
                                        placeholder="أدخل سعر الشراء" />
                                </td>
                                @foreach ($salesSegments as $segment)
                                    <td>
                                        <input type="number" class="form-control" step="0.1"
                                            name="products[{{ $data->id }}][units][{{ $item->unit_id }}][sales_segments][{{ $segment->id }}]"
                                            placeholder="سعر شريحة {{ $segment->name }}"
                                            value="{{ isset($sales_segment_prices[$item->product->id][$segment->id][$item->unit_id]) ? $item->product->getSalePriceByUnitAndSegment($item->unit_id, $segment->id) : '' }}">
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Image Upload -->
                <div class="col-lg-12">
                    <x-form.file class="form-control" name="image" attribute=""
                        label="{{ trans('admin.image') }}" />
                </div>
            </div>
    </div>
@endforeach
