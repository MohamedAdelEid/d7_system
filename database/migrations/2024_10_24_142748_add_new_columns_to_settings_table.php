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
            $table->string('site_image')->nullable();
            $table->float('default_credit_limit')->default(0);
            $table->boolean('display_brands')->default(true);
            $table->boolean('display_main_category')->default(true);
            $table->boolean('display_sub_category')->default(true);
            $table->boolean('display_sub_units')->default(true);
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
            //
        });
    }
};
