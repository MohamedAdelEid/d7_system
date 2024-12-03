<?php

namespace App\Imports;

use Exception;
use App\Models\Unit;
use App\Models\Brand;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Category;
use App\Models\SalesSegment;
use App\Models\ProductUnitDetail;
use App\Models\ProductUnitDetails;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\SalesSegmentProduct;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows)
    {
        Log::info($rows);
        foreach ($rows as $row) {
            try {
                DB::beginTransaction();

                // Handle Branches
                $branches = explode(',', $row['branches']);
                $branch_ids = [];
                foreach ($branches as $branch) {
                    $branchData = Branch::where('name', trim($branch))->first();
                    if (!$branchData) {
                        throw new Exception('Branch ' . $branch . ' does not exist.');
                    }
                    $branch_ids[] = $branchData->id;
                }

                // Handle Category
                $main_category = Category::where('name', trim($row['main_category_name']))->first();
                if (!$main_category) {
                    throw new Exception('الفئة الرئيسية ' . $row['main_category_name'] . ' غير موجودة.');
                }
                
                // Handle Sub Category (nullable)
                $category = null;
                if (!empty($row['sub_category_name'])) {
                    $category = Category::where('name', trim($row['sub_category_name']))->first();
                    if (!$category) {
                        throw new Exception('الفئة الفرعية ' . $row['sub_category_name'] . ' غير موجودة.');
                    }
                }

                // Handle Brand (nullable)
                $brand = null;
                if (!empty($row['brand_name'])) {
                    $brand = Brand::where('name', trim($row['brand_name']))->first();
                    if (!$brand) {
                        throw new Exception('العلامة التجارية ' . $row['brand_name'] . ' غير موجودة.');
                    }
                }

                // Handle Primary Unit
                $base_unit = Unit::where('actual_name', trim($row['main_unit_name']))->first();
                if (!$base_unit) {
                    throw new Exception('الوحدة الرئيسية ' . $row['main_unit_name'] . ' غير موجودة.');
                }
                $base_unit_id = $base_unit->id;

                // Handle Sub Units (nullable)
                $sub_unit_ids = [];
                $sub_unit_id = null;
                if (!empty($row['sub_unit_name'])) {
                    $sub_unit = Unit::where('actual_name', trim($row['sub_unit_name']))->first();
                    if ($sub_unit) {
                        $sub_unit_id = $sub_unit->id;
                        $sub_unit_ids = [$sub_unit_id];
                    }
                }

                // Prepare product data
                $productData = [
                    'name' => $row['name'],
                    'description' => $row['description'] ?? null,
                    'main_category_id' => $main_category->id,
                    'category_id' => $category ? $category->id : null,
                    'unit_id' => $base_unit_id,
                    'sub_unit_ids' => $sub_unit_ids,
                    'max_sale' => $row['max_sale'] ?? 0,
                    'min_sale' => $row['min_sale'] ?? 0,
                    'enable_stock' => 1,
                    'quantity_alert' => $row['quantity_alert'] ?? 0,
                    'for_sale' => $row['for_sale'] ?? 1,
                ];

                // Add brand_id only if brand exists
                if ($brand) {
                    $productData['brand_id'] = $brand->id;
                }

                // Create or Update Product
        
                $product = Product::updateOrCreate(
                    ['sku' => $this->generateNewSku($row['sku'])],
                    $productData
                );

                // Sync Branches
                $product->Branches()->sync($branch_ids);

                // Handle Main Unit Details
                ProductUnitDetails::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'unit_id' => $base_unit_id,
                    ],
                    [
                        'sale_price' => $row['main_unit_sale_price'] ?? 0,
                        'purchase_price' => $row['main_unit_purchase_price'] ?? 0,
                    ]
                );

                // Handle Sub Unit Details (only if sub unit exists)
                if ($sub_unit_id) {
                    ProductUnitDetails::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'unit_id' => $sub_unit_id,
                        ],
                        [
                            'sale_price' => $row['sub_unit_sale_price'] ?? 0,
                            'purchase_price' => $row['sub_unit_purchase_price'] ?? 0,
                        ]
                    );
                }

                // Handle Sales Segment Prices
                if (!empty($row['sales_segment_prices'])) {
                    $salesSegmentPrices = explode(',', $row['sales_segment_prices']);
                    foreach ($salesSegmentPrices as $segmentPrice) {
                        $segmentData = explode(':', $segmentPrice);
                        
                        // Validate segment data format
                        if (count($segmentData) !== 3) {
                            continue; // Skip invalid format
                        }

                        $salesSegmentId = SalesSegment::firstOrCreate(['name' => trim($segmentData[0])])->id;
                        $segmentUnit = Unit::where('actual_name', trim($segmentData[1]))->first();
                        
                        if (!$segmentUnit) {
                            continue; // Skip if unit doesn't exist
                        }

                        SalesSegmentProduct::updateOrCreate(
                            [
                                'sales_segment_id' => $salesSegmentId,
                                'product_id' => $product->id,
                                'unit_id' => $segmentUnit->id,
                            ],
                            [
                                'price' => trim($segmentData[2]) ?? 0,
                            ]
                        );
                    }
                }

                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                Log::error('Product Import Error: ' . $e->getMessage());
                throw new Exception(message: 'خطأ في استيراد المنتج ' . ($row['name'] ?? 'Unknown') . ': ' . $e->getMessage());
            }
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'sku' => 'nullable|integer|unique:products,sku',
            'description' => 'nullable|string',
            'main_unit_name' => 'required|string',
            'main_unit_sale_price' => 'required|numeric',
            'main_unit_purchase_price' => 'required|numeric',
            'sub_unit_name' => 'nullable|string',
            'sub_unit_sale_price' => 'nullable|numeric',
            'sub_unit_purchase_price' => 'nullable|numeric',
            'brand_name' => 'nullable|string',
            'main_category_name' => 'required|string',
            'sub_category_name' => 'nullable|string',
            'min_sale' => 'nullable|numeric',
            'max_sale' => 'nullable|numeric',
            'quantity_alert' => 'nullable|integer',
            'for_sale' => 'nullable|in:0,1',
            'branches' => 'required|string',
            'sales_segment_prices' => 'nullable|string',
            'image' => 'nullable|string',
        ];
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
}