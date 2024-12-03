<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-3">
                @include('components.form.input', [
                    'class' => 'form-control',
                    'name' => "sku",
                    'label' => trans('admin.sku'),
                    'value' => $product->sku,
                    'attribute' => 'required disabled',
                ])
            </div>
            <div class="col-lg-3">
                @include('components.form.input', [
                    'class' => 'form-control',
                    'name' => "name",
                    'label' => trans('admin.name'),
                    'value' => $product->name,
                    'attribute' => 'required disabled',
                ])
            </div>
            @if($product->Branches->count() > 0)
            @foreach($product->Branches as $branch)
            <div class="col-lg-3">
                @include('components.form.input', [
                    'class' => 'form-control',
                    'name' => "qty_available_by_main_unit",
                    'label' => trans('admin.qty_available_by_main_unit_in_branch', ['branch' => $branch->name]),
                    'value' => $product->getStockByMainUnit($branch->id),
                    'attribute' => 'required disabled',
                ])
            </div>
            @endforeach
            @endif
            @if($product->Branches->count() > 0)
            @foreach($product->Branches as $branch)
            <div class="col-lg-3">
                @include('components.form.input', [
                    'class' => 'form-control',
                    'name' => "qty_available_by_sub_unit",
                    'label' => trans('admin.qty_available_by_sub_unit_in_branch', ['branch' => $branch->name]),
                    'value' => $product->getStockBySubUnit($branch->id)['stock_in_sub_unit'],
                    'attribute' => 'required disabled',
                ])
            </div>
            @endforeach
            @endif
        </div>
    </div>
    <div class="col-lg-12 mt-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ trans('admin.units_details') }}</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped responsive">
                    <thead>
                    <tr>
                        <th>{{ trans('admin.unit') }}</th>
                        @if ($settings->display_main_category)
                        <th>{{ trans('admin.main category') }}</th>
                        @endif
                        @if($settings->display_sub_category)
                        <th>{{ trans('admin.sub category') }}</th>
                        @endif
                        @if($settings->display_brands)
                        <th>{{ trans('admin.brand') }}</th>
                        @endif
                        <th>{{ trans('admin.for_sale') }}</th>
                        <th>{{ trans('admin.purchase_price') }}</th>
                        <th>{{ trans('admin.unit price') }}</th>
                        <th>{{ trans('admin.Created at') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                        {{-- Main unit --}}
                       
    
                        {{-- Sub units --}}
                        @foreach($product->GetAllUnits() as $unit)
                            @php
                                $unitDetails = $product->ProductUnitDetails()
                                    ->where('unit_id', $unit->id)
                                    ->first();
                            @endphp
                            <tr>
                                <td>{{ $unit->actual_name ?? tans('admin.no_nuit') }}</td>
                                @if($settings->display_main_category)
                                <td>{{$product->Category->name ?? trans('admin.no_main_category')}}</td>
                                @endif
                                @if($settings->display_sub_category)
                                <td>{{$product->SubCategory->name ?? trans('admin.no_sub_category')}}</td>
                                @endif
                                @if($settings->display_brands)
                                <td>{{$product->brand->name ?? trans('admin.no_brand')}}</td>
                                @endif
                                <td>{{$product->for_sale ? 'متاح للبيع' : 'غير متاح للبيع'}}</td>
                                <td>{{ $unitDetails->purchase_price ?? '-' }}</td>
                                <td>{{ $unitDetails->sale_price ?? '-' }}</td>
                                <td>{{\Carbon\Carbon::parse($product->created_at)->format('d-m-Y h:i')}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        
    </div>
</div>