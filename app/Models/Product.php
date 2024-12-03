<?php

namespace App\Models;

use App\Traits\Stock;
use App\Traits\helper;
use App\Traits\generalModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    use generalModel, Stock;
    protected $table = 'products';
    public $guarded = [];

    protected $casts = [
        'sub_unit_ids' => 'array',
    ];


    public function Brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function scopeHasStock($query, $branch_id)
    {
        $query->whereHas('Branches', function ($query) use ($branch_id) {
            $query->where('branch_id', $branch_id);
        });
        return $query;
    }

    public function PriceHistories()
    {
        return $this->hasMany(ProductPriceHistory::class);
    }

    public function units()
    {
        return $this->hasManyThrough(
            Unit::class,
            ProductUnitDetails::class,
            'product_id',
            'id',
            'id',
            'unit_id'
        );
    }

    public function MainUnit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function GetAllUnits()
    {
        return Unit::whereIn('id', $this->sub_unit_ids)->get();
    }
    public function getSubUnit()
    {
        return $this->GetAllUnits()->first();
    }
    public function TransactionSellLines()
    {
        return $this->hasMany(TransactionSellLine::class, 'product_id');
    }

    public function TransactionPurchaseLines()
    {
        return $this->hasMany(TransactionPurchaseLine::class, 'product_id');
    }
    public function SpoiledLines()
    {
        return $this->hasMany(SpoiledLine::class, 'product_id');
    }
    public function Category()
    {
        return $this->belongsTo(Category::class, 'main_category_id');
    }
    public function SubCategory()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function Branches()
    {
        return $this->belongsToMany(Branch::class, 'product_branch', 'product_id', 'branch_id');
    }


    public function getStock()
    {
        $qty_available = ProductBranchDetails::where('product_id', $this->id)
            ->sum('qty_available');


        return $qty_available;
    }

    public function getStockByBranch($branch_id)
    {
        $ProductBranchDetails = ProductBranchDetails::where('product_id', $this->id)
            ->where('branch_id', $branch_id)
            ->first();

        if (!$ProductBranchDetails) {

            return 0;
        }

        return $ProductBranchDetails->qty_available;
    }
    public function getStockByMainUnit($branch_id)
    {
        return $this->getStockByBranch($branch_id) * $this->MainUnit->getMultiplier();
    }
    public function ProductBranch()
    {
        return $this->hasMany(ProductBranch::class, 'product_id');
    }
    public function getSalePriceByUnit($unit_id)
    {
        $productUnitDetails = ProductUnitDetails::where('unit_id', $unit_id)
            ->where('product_id', $this->id)
            ->first();



        return $productUnitDetails ? $productUnitDetails->sale_price : null;
    }

    public function getSalePriceByUnitAndSegment($unit_id, $salesSegmentId)
    {
        $salesSegment = $this->salesSegments()->where('sales_segment_id', $salesSegmentId)
            ->where('unit_id', $unit_id)
            ->first();

        if ($salesSegment) {
            return $salesSegment->pivot->price;
        }

        return null;
    }


    public function getPurchasePriceByUnit($unit_id)
    {
        $productUnitDetails = ProductUnitDetails::where('unit_id', $unit_id)
            ->where('product_id', $this->id)
            ->first();
        return $productUnitDetails->purchase_price ?? "";
    }

    public function getMainUnitName($unit_id)
    {
        $unit = Unit::find($unit_id);

        return $unit->actual_name;
    }

    public function getQuantityByUnit($product, $unit_id, $quantity)
    {
        if ($product->unit_id == $unit_id) {
            return $quantity;
        }

        $unit = Unit::find($unit_id);
        
        if (!$unit) {
            return $quantity;
        }

        if ($unit->base_unit_id == $product->unit_id) {
            return $quantity * $unit->base_unit_multiplier;
        } else {
            return $quantity / $unit->base_unit_multiplier;
        }
    }
    public function getStockBySubUnit($branch_id)
    {
        $stockByBranch = $this->getStockByBranch($branch_id);
        
        $mainUnit = Unit::find($this->unit_id);
        
        $subUnitIds = $this->sub_unit_ids;
        if (is_string($subUnitIds)) {
            $subUnitIds = json_decode($subUnitIds, true);
        }
        
        if (empty($subUnitIds)) {
            return [
                'stock_in_sub_unit' => $stockByBranch,
                'sub_unit_details' => $mainUnit
            ];
        }
        $subUnit = Unit::find($subUnitIds[0]);
        if (!$subUnit) {
            return [
                'stock_in_sub_unit' => $stockByBranch,
                'sub_unit_details' => $mainUnit
            ];
        }

        // Calculate stock in sub unit
        $stockInSubUnit = $this->getQuantityByUnit($this, $subUnit->id, $stockByBranch);

        return [
            'stock_in_sub_unit' => $stockInSubUnit,
            'sub_unit_details' => $subUnit
        ];
    }
    public function Image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function ProductBranchDetails()
    {
        return $this->hasMany(ProductBranchDetails::class, 'product_id');
    }
    public function ProductUnitDetails()
    {
        return $this->hasMany(ProductUnitDetails::class, 'product_id');
    }

    //
    public function getImage()
    {
        if ($this->Image != null) {
            return url('uploads/products/' . $this->Image->src);
        } else {
            return url('default.png');
        }
    }
    public function transferLines()
    {
        return $this->hasMany(TransferLine::class, 'product_id');
    }
    public function salesSegments()
    {
        return $this->belongsToMany(SalesSegment::class, 'sales_segment_products', 'product_id', 'sales_segment_id')->withPivot('price', 'unit_id');
    }
    public function getPriceBySalesSegment($sales_segment_id)
    {
        return $this->salesSegments()->where('sales_segment_id', $sales_segment_id)->first()->pivot->price;
    }
    public function getSellPrice()
    {
        $productUnitDetail = $this->ProductUnitDetails()->where('unit_id', $this->MainUnit->id)->first();
        return $productUnitDetail ? $productUnitDetail->sale_price : null;
    }

    public function getPurchasePrice()
    {
        $productUnitDetail = $this->ProductUnitDetails()->where('unit_id', $this->MainUnit->id)->first();
        return $productUnitDetail ? $productUnitDetail->purchase_price : null;
    }

    public function getProductStatistics($branch_id = null)
    {
        $openStock = $this->TransactionPurchaseLines()
            ->whereHas('Transaction', function ($query) use ($branch_id) {
                $query->where('type', 'opening_stock')
                    ->when($branch_id, function ($q) use ($branch_id) {
                        return $q->where('branch_id', $branch_id);
                    });
            })->get()    ->sum(function ($openStock) {
        
                $quantity = $this->getMainUnitQuantityFromSubUnit(
                    $this, 
                    $openStock->unit_id, 
                    $openStock->quantity
                );
        
                return $quantity; 
            });

        $totalPurchase = $this->TransactionPurchaseLines()
            ->whereHas('Transaction', function ($query) use ($branch_id) {
                $query->where('type', 'purchase')
                    ->where('delivery_status', 'delivered')
                    ->when($branch_id, function ($q) use ($branch_id) {
                        return $q->where('branch_id', $branch_id);
                    });
            })
            ->get()->sum(function ($purchase) {
        
                $quantity = $this->getMainUnitQuantityFromSubUnit(
                    $this, 
                    $purchase->unit_id, 
                    $purchase->return_quantity + $purchase->quantity
                );
        
                return $quantity; 
            });

        $totalSales = $this->TransactionSellLines()
            ->whereHas('Transaction', function ($query) use ($branch_id) {
                $query->where('type', 'sell')
                    ->when($branch_id, function ($q) use ($branch_id) { 
                        return $q->where('branch_id', $branch_id);
                    });
            })->get()->sum(function ($sell) {
        
                $quantity = $this->getMainUnitQuantityFromSubUnit(
                    $this, 
                    $sell->unit_id, 
                    $sell->quantity+
                    $sell->return_quantity
                );
        
                return $quantity; 
            });
            $totalSpoiled = $this->SpoiledLines()
            ->whereHas('Transaction', function ($query) use ($branch_id) {
                $query->when($branch_id, function ($q) use ($branch_id) {
                    $q->where('branch_id', $branch_id)
                      ->where('status', 'final');
                });
            })
            ->get() 
            ->sum(function ($line) {
                return $this->getMainUnitQuantityFromSubUnit(
                    $this,
                    $line->unit_id,
                    $line->quantity
                );
            });

            $totalTransfer = $this->transferLines()
            ->whereHas('Transaction', function ($query) use ($branch_id) {
                $query->when($branch_id, function ($q) use ($branch_id) {
                    $q->where('branch_id', $branch_id)
                      ->where('status', 'final');
                });
            })
            ->get() 
            ->sum(function ($transferLine) {
        
                $quantity = $this->getMainUnitQuantityFromSubUnit(
                    $this, 
                    $transferLine->unit_id, 
                    $transferLine->quantity
                );
        
                return $quantity; 
            });

        $totalReturnPurchase = $this->TransactionPurchaseLines()
            ->whereHas('Transaction', function ($query) use ($branch_id) {
                $query->where('type', 'purchase')
                    ->when($branch_id, function ($q) use ($branch_id) {
                        return $q->where('branch_id', $branch_id);
                    });
            })->get()->sum(function ($purchase) {
        
                $quantity = $this->getMainUnitQuantityFromSubUnit(
                    $this, 
                    $purchase->unit_id, 
                    $purchase->return_quantity
                );
        
                return $quantity; 
            });

        $totalReturnSales = $this->TransactionSellLines()
            ->whereHas('Transaction', function ($query) use ($branch_id) {
                $query->where('type', 'sell')
                    ->when($branch_id, function ($q) use ($branch_id) {
                        return $q->where('branch_id', $branch_id);
                    });
            })->get()->sum(function ($returnSales) {
        
                $quantity = $this->getMainUnitQuantityFromSubUnit(
                    $this, 
                    $returnSales->unit_id, 
                    $returnSales->return_quantity
                );
        
                return $quantity; 
            });

        $quantity = $openStock + $totalPurchase - $totalSales - $totalSpoiled + $totalTransfer - $totalReturnPurchase + $totalReturnSales;

        return [
            'open_stock' => $openStock,
            'total_purchase' => $totalPurchase,
            'total_sales' => $totalSales,
            'total_spoiled' => $totalSpoiled,
            'total_transfer' => $totalTransfer,
            'total_return_purchase' => $totalReturnPurchase,
            'total_return_sales' => $totalReturnSales,
            'quantity' => $quantity,
        ];
    }



}
