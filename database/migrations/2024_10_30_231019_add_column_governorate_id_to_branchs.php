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
        Schema::table('branchs', function (Blueprint $table) {
           
            if (!Schema::hasColumn('branchs', 'governorate_id')) {
                $table->unsignedBigInteger('governorate_id')->nullable();
            }
            
   
            $table->foreign('governorate_id')->references('id')->on('governorates')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('branchs', function (Blueprint $table) {
       
            $table->dropForeign(['governorate_id']);
      
            $table->dropColumn('governorate_id');
        });
    }
};

