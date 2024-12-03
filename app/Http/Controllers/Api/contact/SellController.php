<?php

namespace App\Http\Controllers\Api\contact;

use App\Traits\Stock;
use App\Models\Branch;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\SellService;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use App\Services\ActivityLogsService;
use App\Services\PaymentTransactionService;

class SellController extends Controller
{
    use Stock;

    public $SellService , $TransactionService , $StockService;
    protected $PaymentTransactionService;

    public function __construct(PaymentTransactionService $PaymentTransactionService,SellService $SellService,TransactionService $TransactionService, StockService $StockService,)
    {
        $this->SellService = $SellService;
        $this->PaymentTransactionService = $PaymentTransactionService;
        $this->TransactionService = $TransactionService;
        $this->StockService = $StockService ;
    }


    // store order
    public function checkOut(Request $request)
    {

        DB::beginTransaction();

        try {
            $branch = Branch::find($request->branch_id);
            $contact = auth('contact_api')->user();
            
            if (!$contact) {
                return response()->json([
                    'successful' => false,
                    'status' => 'E01',
                    'message' => 'Unauthorized access.',
                ], 401);
            }
            if (!$branch) {
                return response()->json([
                    'successful' => false,
                    'status' => 'E02',
                    'message' => 'Branch not found.',
                ], 404);
            }


            // Prepare sale data
            $data = [
                'branch_id' => $branch->id,
                'contact_id' => 7,
                'payment_type' => 'credit',
                'status' => "pending",
                'account_id' => $branch->credit_account_id,
                'transaction_from' => 'mobile',
                'discount_value' => $request->discount_value,
                'discount_type' => $request->discount_type,
                'date' => $request->date,
            ];

            // Create the sale transaction via SellService
            $transaction = $this->SellService->CreateSell($data, $request->products, $request);

            // Commit the transaction to the database
            DB::commit();

            // Return success response with transaction details
            return response()->json([
                'successful' => true,
                'status' => 'S01',
                'message' => trans('admin.success'),
                'data' => $transaction,
            ], 201);
        } catch (\Exception $e) {
            // Rollback in case of an error
            DB::rollBack();

            // Return error response
            return response()->json([
                'successful' => false,
                'status' => 'E03',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    // update order
    public function updateOrder(Request $request)
    {

        DB::beginTransaction();

        try {

            $contact = auth('contact_api')->user();
    
            if (!$contact) {
                return response()->json([
                    'successful' => false,
                    'status' => 'E01',
                    'message' => 'Unauthorized access.',
                ], 401);
            }

            $transaction = Transaction::find($request->transaction_id);

            if (!$transaction) {
                return response()->json([
                    'successful' => false,
                    'status' => 'E02',
                    'message' => 'Transaction not found.',
                ], 404);
            }
            if ($transaction->status != 'pending' || $transaction->delivery_status != 'ordered')  {
                return response()->json([
                    'successful' => false,
                    'status' => 'E02',
                    'message' => 'Cannot be updated',
                ], 404);
            }
    
            $branch = Branch::find($request->branch_id);
 
            if (!$branch) {
                return response()->json([
                    'successful' => false,
                    'status' => 'E02',
                    'message' => 'Branch not found.',
                ], 404);
            }

            // Prepare the updated sale data
            $data = [
                'branch_id' => $branch->id,
                'contact_id' => $contact->id,
                'discount_value' => $request->discount_value ?? $transaction->discount_value,
                'discount_type' => $request->discount_type ?? $transaction->discount_type,
                'date' => $request->date ?? $transaction->transaction_date,
            ];

            // Update the transaction via SellService
            $this->SellService->UpdateSell($transaction, $data, $request->products, $request);

            // Commit the transaction to the database
            DB::commit();

            // Return success response with updated transaction details
            return response()->json([
                'successful' => true,
                'status' => 'S02',
                'message' => trans('admin.update_success'),
                'data' => $transaction,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            // Return error response
            return response()->json([
                'successful' => false,
                'status' => 'E03',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    // canceled order
    public function canceledOrder(Request $request)
    {
        DB::beginTransaction();

        try {
            $sell = Transaction::findOrFail($request->sell_id);
    
            if ($sell->status == 'canceled') {
                return response()->json([
                    'successful' => false,
                    'status' => 'E04',
                    'message' => 'The transaction has already been canceled.',
                ], 400);
            }
    
            foreach ($sell->TransactionSellLines as $line) {
                $this->StockService->addToStock($line->product_id, $sell->branch_id, $line->main_unit_quantity);
            }
    
            if ($sell->PaymentTransaction) {
                $this->PaymentTransactionService->delete($sell->PaymentTransaction, true);
            } else {
                $this->PaymentTransactionService->ContactSubtract($sell->Contact, $sell->final_price);
            }
            $sell->update(['status' => 'canceled']);
           
            DB::commit();
    
            return response()->json([
                'successful' => true,
                'status' => 'S01',
                'message' => 'The transaction has been successfully canceled.',
            ], 200);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'successful' => false,
                'status' => 'E03',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    
    }


}
