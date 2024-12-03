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
        Schema::table('activity_log', function (Blueprint $table) {
            $table->enum('proccess_type', [ 
                'suppliers', 'customers', 'products', 'sales', 'purchase','stock_transfer','expenses','spoiled_stock','accounts','create', 'update', 'delete'
            ])
                    ->nullable()
                    ->after('title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropColumn('process_type');
        });
    }
};
