<?php

namespace App\Services;

use Exception;
use App\Models\Image;
use App\Traits\Upload;
use App\Models\Product;
use App\Models\ProductUnitDetails;
use App\Models\ProductPriceHistory;
use App\Models\SalesSegmentProduct;
use App\Models\ProductBranchDetails;
use Illuminate\Support\Facades\Hash;
use App\Services\ActivityLogsService;

class ProductService
{
    use Upload;
    public $activityLogsService;
    public function __construct(ActivityLogsService $activityLogsService)
    {
        $this->activityLogsService = $activityLogsService;
    }

    
    public function create(array $data)
    {
        
        $data['sub_unit_ids'][] = $data['unit_id'];
 
        $sku = $this->generateNewSku($data['sku']);
        $product = Product::create([
            'name' => $data['name'],
            'sku' =>  $sku ,
            'description' => $data['description'] ?? null,
            'brand_id' => $data['brand_id'] ?? null,
            'main_category_id' => $data['main_category_id'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'unit_id' => $data['unit_id'],
            'sub_unit_ids' => $data['sub_unit_ids'] ?? [],
            'quantity_alert' => $data['quantity_alert'] ?? null,
            'for_sale' => $data['for_sale'] ?? false,
            'min_sale' => $data['min_sale'] ?? null,
            'max_sale' => $data['max_sale'] ?? null,
        ]);

        if (!empty($data['units'])) {
            foreach ($data['units'] as $unitId => $unitData) {
                ProductUnitDetails::create([
                    'product_id' => $product->id,
                    'unit_id' => $unitId,
                    'sale_price' => $unitData['sale_price'],
                    'purchase_price' => $unitData['purchase_price'],
                ]);

                if (!empty($unitData['sales_segments'])) {
                    foreach ($unitData['sales_segments'] as $segmentId => $price) {
                        if($price != null){

                            SalesSegmentProduct::create([
                                'product_id' => $product->id,
                                'unit_id' => $unitId,
                                'sales_segment_id' => $segmentId,
                                'price' => $price ,
                            ]);
                        }
                    }
                }
            }
        }

        $product->Branches()->sync($data['branch_ids'] ?? []);

        if (!empty($data['image'])) {
            $path = $this->uploadImage($data['image'], 'uploads/products');

            if ($product->Image == null) {
                Image::create([
                    'imageable_id'   => $product->id,
                    'imageable_type' => 'App\Models\Product',
                    'src'            => $path,
                ]);
            }
        }

        $this->activityLogsService->insert([
            'subject' => $product,
            'title' => 'تم اضافة المنتج',
            'description' => 'تم اضافة المنتج ' . $product->name .
                ' والعلامة التجارية ' . ($product->brand ? $product->brand->name : 'غير موجود') .
                ' والفئة ' . ($product->category ? $product->category->name : 'غير موجود') .
                ' والوحدة الرئيسية ' . ($product->unit ? $product->unit->name : 'غير موجود') .
                ' في الفروع ' . implode(', ', $product->Branches->pluck('name')->toArray()) . '.',
            'proccess_type' => 'products',
            'user_id' => auth()->id(),
        ]);

        return $product;
    }

    public function generateNewSku($sku)
    {
        if ($sku) {
            if (!Product::where('sku', $sku)->get()->first()) {
                return $sku;
            }
        }
    
        $maxSku = Product::max('sku');
    
        if (is_null($maxSku)) {
            return 1;
        }
    
        return intval($maxSku) + 1;
    }
    
    
    public function edit(array $data)
    {

        $product = Product::find($data['product_id']);
        if (!$product) {
            throw new Exception('Product not found');
        }

        $sku = $this->generateNewSku($data['sku']);


        $product->update([
            'name' => $data['name'],
            'sku' => $data['sku'] == $product->sku  ? $product->sku : $sku   ,
            'description' => $data['description'] ?? null,
            'brand_id' => $data['brand_id'] ?? null,
            'main_category_id' => $data['main_category_id'] ?? null,
            'unit_id' => $data['unit_id'] ?? null,
            'sub_unit_ids' => $data['sub_unit_ids'] ?? [],
            'category_id' => $data['category_id'] ?? null,
            'quantity_alert' => $data['quantity_alert'] ?? null,
            'min_sale' => $data['min_sale'] ?? null,
            'max_sale' => $data['max_sale'] ?? null,
        ]);


        if (!empty($data['units'])) {
            $updatedUnitIds = [];
        
            foreach ($data['units'] as $unitId => $unitData) {
                $updatedUnitIds[] = $unitId;
                $oldUnitPrice = $product->getSalePriceByUnit($unitId) ?? null;
        
                if ($oldUnitPrice !== null && $oldUnitPrice != $unitData['sale_price']) {
                    ProductPriceHistory::create([
                        'product_id' => $product->id,
                        'unit_id' => $unitId,
                        'old_unit_price' => $oldUnitPrice,
                        'new_unit_price' => $unitData['sale_price'],
                        'changed_by' => auth()->id(),
                    ]);
                }
        
                if (!empty($unitData['sale_price']) || !empty($unitData['purchase_price'])) {
                    ProductUnitDetails::updateOrCreate(
                        ['product_id' => $product->id, 'unit_id' => $unitId],
                        [
                            'sale_price' => $unitData['sale_price'],
                            'purchase_price' => $unitData['purchase_price']
                        ]
                    );
                } else {
                    ProductUnitDetails::where('product_id', $product->id)
                        ->where('unit_id', $unitId)
                        ->delete();
                }
        
                if (!empty($unitData['sales_segments'])) {
                    $updatedSegmentIds = array_keys($unitData['sales_segments']);
        
                    foreach ($unitData['sales_segments'] as $segmentId => $price) {
                        if($price != null){

                            SalesSegmentProduct::updateOrCreate(
                                ['product_id' => $product->id, 'unit_id' => $unitId, 'sales_segment_id' => $segmentId],
                                ['price' => $price ?? null]
                            );
                        }else{
                            SalesSegmentProduct::where('product_id', $product->id)
                            ->where('unit_id', $unitId)
                            ->delete();
                        }
                    }
        
                    SalesSegmentProduct::where('product_id', $product->id)
                        ->where('unit_id', $unitId)
                        ->whereNotIn('sales_segment_id', $updatedSegmentIds)
                        ->delete();
                } else {
                    SalesSegmentProduct::where('product_id', $product->id)
                        ->where('unit_id', $unitId)
                        ->delete();
                }
            }
        
            ProductUnitDetails::where('product_id', $product->id)
                ->whereNotIn('unit_id', $updatedUnitIds) 
                ->delete();
        } else {
            ProductUnitDetails::where('product_id', $product->id)
                ->where('unit_id', '!=', $product->unit_id)
                ->delete();
        }
        
        

        $product->Branches()->sync($data['branch_ids'] ?? []);

        if (!empty($data['image'])) {
            $path = $this->uploadImage($data['image'], 'uploads/products');

            if ($product->Image == null) {
                Image::create([
                    'imageable_id'   => $product->id,
                    'imageable_type' => 'App\Models\Product',
                    'src'            => $path,
                ]);
            } else {
                $oldImage = $product->Image->src;
                if (file_exists(base_path('public/uploads/products/') . $oldImage))
                    unlink(base_path('public/uploads/products/') . $oldImage);
                $product->Image->src = $path;
                $product->Image->save();
            }
        }

        $this->activityLogsService->insert([
            'subject' => $product,
            'title' => 'تم تعديل المنتج',
            'description' => 'تم تعديل المنتج ' . $product->name .
                ' والعلامة التجارية ' . ($product->brand ? $product->brand->name : 'غير موجود') .
                ' والفئة ' . ($product->category ? $product->category->name : 'غير موجود') .
                ' والوحدة الرئيسية ' . ($product->unit ? $product->unit->name : 'غير موجود') .
                ' في الفروع ' . implode(', ', $product->Branches->pluck('name')->toArray()) . '.',
            'proccess_type' => 'products',
            'user_id' => auth()->id(),
        ]);

        return $product;
    }
}
