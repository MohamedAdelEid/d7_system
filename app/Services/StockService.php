<?php

namespace App\Services;

use App\Models\ProductBranchDetails;
use App\Models\Transaction;
use App\Traits\Stock;

class StockService
{
    use Stock;
    public function getTypeOperation($type)
    {
        return Transaction::TYPE[$type];
    }

    public function bulckAddToStockByPurchaseLines($transaction, $TransactionPurchaseLines)
    {
        foreach ($TransactionPurchaseLines as $line) {
            if ($line->quantity) {
                $this->addToStock(
                    $line->product_id,
                    $transaction->branch_id,
                    $line->main_unit_quantity
                );
            }
        }
    }
    public function bulckAddToStockBySpoiledLinesLines($transaction, $TransactionSpoiledLines)
    {
        foreach ($TransactionSpoiledLines as $line) {
            if ($line->quantity) {
                $this->addToStock(
                    $line->product_id,
                    $transaction->branch_id,
                    $line->main_unit_quantity
                );
            }
        }
    }
    public function bulckSubtractFromStockBySpoiledLinesLines($transaction, $TransactionSpoiledLines)
    {
        foreach ($TransactionSpoiledLines as $line) {
            if ($line->quantity) {
                $this->SubtractFromStock(
                    $line->product_id,
                    $transaction->branch_id,
                    $line->main_unit_quantity
                );
            }
        }
    }

    public function bulckSubtractFromStockBySellLines($transaction, $TransactionSellLines)
    {
        foreach ($TransactionSellLines as $line) {
            if ($line->quantity) {
                $this->SubtractFromStock(
                    $line->product_id,
                    $transaction->branch_id,
                    $line->main_unit_quantity
                );
            }
        }
    }

    public function bulckAddToStockByRetunLines($transaction, $TransactionRetunLines)
    {
        foreach ($TransactionRetunLines as $line) {
            if ($line->quantity) {
                $this->addToStock(
                    $line->product_id,
                    $transaction->branch_id,
                    $line->main_unit_quantity
                );
            }
        }
    }

    public function bulckSubtractFromStockByRetunLines($transaction, $TransactionRetunLines)
    {
        foreach ($TransactionRetunLines as $line) {
            if ($line->quantity) {
                $this->SubtractFromStock(
                    $line->product_id,
                    $transaction->branch_id,
                    $line->main_unit_quantity
                );
            }
        }
    }

    public function addToStock($product_id, $branch_id, $quantity, $unit_id = null)
    {
        $ProductBranchDetails = $this->getProductBranchDetails($product_id, $branch_id);

        $mainQuantity = $this->getMainUnitQuantityFromSubUnit(
            $ProductBranchDetails->Product,
            $unit_id,
            $quantity
        );

        $ProductBranchDetails->qty_available += $mainQuantity;
        $ProductBranchDetails->save();
    }

    public function SubtractFromStock($product_id, $branch_id, $quantity, $unit_id = null)
    {
        $ProductBranchDetails = $this->getProductBranchDetails($product_id, $branch_id);

        $mainQuantity = $this->getMainUnitQuantityFromSubUnit(
            $ProductBranchDetails->Product,
            $unit_id,
            $quantity
        );
        $ProductBranchDetails->qty_available -= $mainQuantity;
        $ProductBranchDetails->save();
    }

    public function history($product, $branch_id)
    {
        $data = [];
        $units = $product->sub_unit_ids;
        $mainUnit_id = $product->MainUnit->id;
        $mainUnit_id = array_search($mainUnit_id, $units);
       
        if ($mainUnit_id !== false) {
     
            unset($units[$mainUnit_id]);

            $units = array_values($units);
        }
        // dd($units); 
        $sub_unit = $units[0] ?? null;
        //purchaseLines
        $transactionPurchaseLines = $product->TransactionPurchaseLines()
            ->with('Transaction')
            ->with('Transaction.Contact')
            ->whereHas('Transaction', function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id)
                    ->where(function ($query) {
                        $query->where('type', 'purchase')
                            ->where('delivery_status', 'delivered')
                            ->orWhere('type', '!=', 'purchase');
                    });
            })->get();

        foreach ($transactionPurchaseLines as $line) {
            $return_quantity = 0;
            if ($line->return_quantity > 0) {
                $ProductBranchDetails = $this->getProductBranchDetails($line->product_id, $line->Transaction->branch_id);
                $return_quantity = $this->getMainUnitQuantityFromSubUnit(
                    $ProductBranchDetails->Product,
                    $line->unit_id,
                    $line->return_quantity
                );
            }

            $quantity_by_sub_unit = $this->getQuantityByUnit($product,$sub_unit,($line->main_unit_quantity + $return_quantity));
            $change_quantity_by_subunit = "لا يوجد وحده فرعيه";
            if ($sub_unit) {
                $change_quantity_by_subunit = $quantity_by_sub_unit * $this->getTypeOperation($line->transaction->type);
            }
          
            array_push($data, [
                'change_quantity'  => ($line->main_unit_quantity + $return_quantity) * $this->getTypeOperation($line->transaction->type),
                'change_quantity_by_subunit'  => $change_quantity_by_subunit ,
                'created_at'  => date("Y-m-d h:i a", strtotime($line->created_at)),
                'created_at_timestamp'  => $line->created_at,
                'unit_price'  => $line->unit_price,
                'ref_no'  => '<a href="#" style="color: blue;" class="fire-popup" data-url="' . route('dashboard.purchases.show', $line->transaction->id) . '" data-toggle="modal" data-target="#modal-default-big">' . $line->transaction->ref_no . '</a>',
                'type'  => $line->transaction->type,
                'created_by'  => $line->transaction->CreatedBy?->name,
                'contact_name'  => $line->transaction->contact->name ?? "",
            ]);
        }   

 
        //sell line
        $TransactionSellLines =  $product->TransactionSellLines()
            ->with('Transaction')
            ->with('Transaction.Contact')
            ->whereHas('Transaction', function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->get();

        foreach ($TransactionSellLines as $line) {
            $return_quantity = 0;
            if ($line->return_quantity > 0) {
                $ProductBranchDetails = $this->getProductBranchDetails($line->product_id, $line->Transaction->branch_id);
                $return_quantity = $this->getMainUnitQuantityFromSubUnit(
                    $ProductBranchDetails->Product,
                    $line->unit_id,
                    $line->return_quantity
                );
            }
            $quantity_by_sub_unit = $this->getQuantityByUnit($product,$sub_unit,($line->main_unit_quantity + $return_quantity));
            $change_quantity_by_subunit = "لا يوجد وحده فرعيه";
            if ($sub_unit) {
                $change_quantity_by_subunit = $quantity_by_sub_unit * $this->getTypeOperation($line->transaction->type);
            }
          
            array_push($data, [
                'change_quantity'  => ($line->main_unit_quantity + $return_quantity) * $this->getTypeOperation($line->transaction->type),
                'change_quantity_by_subunit'  => $change_quantity_by_subunit,
                'created_at'  => date("Y-m-d h:i a", strtotime($line->created_at)),
                'created_at_timestamp'  => $line->created_at,
                'unit_price'  => $line->unit_price,
                'ref_no'  => '<a href="#" style="color: blue;" class="fire-popup" data-url="' . route('dashboard.sells.show', $line->transaction->id) . '" data-toggle="modal" data-target="#modal-default-big">' . $line->transaction->ref_no . '</a>',
                'type'  => $line->transaction->type,
                'created_by'  => $line->transaction->CreatedBy?->name,
                'contact_name'  => $line->transaction->contact->name ?? "",
            ]);
        }

        //SpoiledLines
        $TransactionSpoiledLines =  $product->SpoiledLines()
            ->with('Transaction')
            ->whereHas('Transaction', function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id)->where('status', 'final');
            })
            ->get();

        foreach ($TransactionSpoiledLines as $line) {
            $quantity_by_sub_unit = $this->getQuantityByUnit($product,$sub_unit,$line->main_unit_quantity);
            $change_quantity_by_subunit = "لا يوجد وحده فرعيه";
            if ($sub_unit) {
                $change_quantity_by_subunit = $quantity_by_sub_unit * $this->getTypeOperation($line->transaction->type);
            }
            array_push($data, [
                'change_quantity'  => $line->main_unit_quantity * $this->getTypeOperation($line->transaction->type),
                'change_quantity_by_subunit'  => $change_quantity_by_subunit,
                'created_at'  => date("Y-m-d h:i a", strtotime($line->created_at)),
                'created_at_timestamp'  => $line->created_at,
                'unit_price'  => $line->unit_price,
                'ref_no'  =>  "",
                'type'  => $line->transaction->type,
                'created_by'  => $line->transaction->CreatedBy?->name,
            ]);
        }
    
        //transfer lines (from)
        $TransactionTransferLines =  $product->TransferLines()
            ->with('Transaction')
            ->whereHas('Transaction', function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
                $query->orWhere('branch_to_id', $branch_id)
                    ->where('status', 'final');
            })
            ->get();

        foreach ($TransactionTransferLines as $line) {
            $quantity = $line->main_unit_quantity;
            if ($line->Transaction->branch_to_id == $branch_id)
            {
                $quantity *= -1;
            }
               
            $quantity_by_sub_unit = $this->getQuantityByUnit($product,$sub_unit,$quantity);
            if ($sub_unit) {
                $change_quantity_by_subunit = $quantity_by_sub_unit * $this->getTypeOperation($line->transaction->type);
            }
            array_push($data, [
                'change_quantity'  => $quantity * $this->getTypeOperation($line->transaction->type),
                'change_quantity_by_subunit'  => $change_quantity_by_subunit,
                'created_at'  => date("Y-m-d h:i a", strtotime($line->created_at)),
                'created_at_timestamp'  => $line->created_at,
                'unit_price'  => $line->unit_price,
                'ref_no'  =>  "",
                'type'  => $line->transaction->type,
                'created_by'  => $line->transaction->CreatedBy?->name,
            ]);
        }

        usort($data, function ($a, $b) {
            return strtotime($a['created_at_timestamp']) - strtotime($b['created_at_timestamp']);
        });

        //process
        $quantity = 0;
        $quantity_by_sub_unit = 0;
        foreach ($data as $key => $item) {
            $quantity = $quantity + $item['change_quantity'];
            if ($sub_unit) {
                $quantity_by_sub_unit = $quantity_by_sub_unit + $item['change_quantity_by_subunit'];
            } else {
                $quantity_by_sub_unit = "";
            }
           
            $data[$key]['quantity'] = $quantity;
            
            $data[$key]['quantity_by_subunit'] = $quantity_by_sub_unit;

            $data[$key]['change_quantity_string'] = ($item['change_quantity'] > 0) ? '+' . $item['change_quantity'] : $item['change_quantity'];
            if ($sub_unit) {
                $data[$key]['change_quantity_string_by_subunit'] = ($item['change_quantity_by_subunit'] > 0) ? '+' . $item['change_quantity_by_subunit'] : $item['change_quantity_by_subunit'];
            } else {
                $data[$key]['change_quantity_string_by_subunit'] = "";
            }
           
        }

        usort($data, function ($a, $b) {
            return strtotime($b['created_at_timestamp']) - strtotime($a['created_at_timestamp']);
        });
        return [
            'data' => $data,
            'quantity' => $quantity,
        ];
    }

    public function getProductBranchDetails($product_id, $branch_id)
    {
        $ProductBranchDetails = ProductBranchDetails::where('product_id', $product_id)
            ->where('branch_id', $branch_id)
            ->first();

        if ($ProductBranchDetails)
            return $ProductBranchDetails;

        return ProductBranchDetails::create([
            'product_id'    => $product_id,
            'branch_id' => $branch_id,
        ]);
    }
}
