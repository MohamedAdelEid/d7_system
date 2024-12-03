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
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id')->unique();

            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branchs')->onDelete('cascade');

            $table->enum('type', ["purchase", "opening_stock", "transfer"])->nullable();
            $table->string('status')->default("pending");
            $table->string('ref_no')->nullable();

            $table->timestamp('transaction_date')->nullable();
            $table->float('total')->nullable();

            $table->softDeletes();
            $table->timestamps();
            
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
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
