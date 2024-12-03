<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions_sell_lines', function (Blueprint $table) {
            $table->unsignedBigInteger('transactions_purchase_line_id')->nullable();
            $table->foreign('transactions_purchase_line_id')->references('id')->on('transactions_purchase_lines')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions_sell_lines', function (Blueprint $table) {
            //
        });
    }
};
