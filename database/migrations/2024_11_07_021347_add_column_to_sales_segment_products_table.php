<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        
        Schema::table('sales_segment_products', function (Blueprint $table) {
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_segment_products', function (Blueprint $table) {
            Schema::table('sales_segment_products', function (Blueprint $table) {
                $table->dropForeign(['unit_id']);
            });
        });
    }
};
