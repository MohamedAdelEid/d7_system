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
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('allow_unit_price_update')->default(false)->after('time_zone');
            $table->boolean('prevent_buy_below_purchase_price')->default(true)->after('allow_unit_price_update');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('allow_unit_price_update');
            $table->dropColumn('prevent_buy_below_purchase_price');
        });
    }
};

