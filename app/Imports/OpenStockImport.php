<?php

use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductBranch;
use App\Models\ProductBranchDetail;
use App\Models\ProductBranchDetails;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OpenStockImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Step 1: Check if the product exists by SKU
        $product = Product::where('sku', $row['sku'])->first();
        if (!$product) {
            // Optionally log the error or skip the row
            return null;
        }

        // Step 2: Check if the branch exists by name and get branch_id
        $branch = Branch::where('name', $row['branch_name'])->first();
        if (!$branch) {
            return null;
        }

        // Step 3: Check if this product is already linked to the branch in product_branch table
        $productBranch = ProductBranch::firstOrCreate([
            'product_id' => $product->id,
            'branch_id' => $branch->id
        ]);

        // Step 4: Insert or update the quantity in product_branch_details table
        return ProductBranchDetails::updateOrCreate(
            [
                'product_id' => $product->id,
                'branch_id' => $branch->id
            ],
            [
                'qty_available' => $row['quantity']
            ]
        );
    }
}
