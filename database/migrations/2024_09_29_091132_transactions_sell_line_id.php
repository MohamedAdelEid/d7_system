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
        Schema::table('transactions_purchase_lines', function (Blueprint $table) {
            $table->unsignedBigInteger('transactions_sell_line_id')->nullable();
            $table->foreign('transactions_sell_line_id')->references('id')->on('transactions_sell_lines')->onDelete('cascade');
        });

        Schema::drop('transactions_return_lines');
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
