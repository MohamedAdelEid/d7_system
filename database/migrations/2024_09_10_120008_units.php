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
        Schema::create('units', function (Blueprint $table) {
            $table->bigIncrements('id')->unique();
            $table->string('actual_name')->nullable();
            $table->string('short_name')->nullable();
            $table->float('base_unit_multiplier', 10, 2)->nullable();
            $table->boolean('base_unit_is_largest')->default(0);
            $table->softDeletes();

            $table->unsignedBigInteger('base_unit_id')->nullable();
            $table->foreign('base_unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
