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
            $table->boolean('display_total_in_invoice')->default(true); // To control total display
            $table->boolean('display_discount_in_invoice')->default(true); // To control discount display
            $table->boolean('display_final_price_in_invoice')->default(true); // To control final price after discount
            $table->boolean('display_credit_details_in_invoice')->default(true); // To control previous and after due amounts
            $table->boolean('display_contact_info_in_invoice')->default(true); // To control contact details display
            $table->boolean('display_branch_info_in_invoice')->default(true); // To control branch info display
            $table->boolean('display_invoice_date_in_invoice')->default(true); // To control invoice date display
            $table->boolean('display_created_by_in_invoice')->default(true); // To control created by display
            $table->boolean('display_ref_no_in_invoice')->default(true); // To control ref no display
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
            $table->dropColumn('display_total_in_invoice');
            $table->dropColumn('display_discount_in_invoice');
            $table->dropColumn('display_final_price_in_invoice');
            $table->dropColumn('display_credit_details_in_invoice');
            $table->dropColumn('display_contact_info_in_invoice');
            $table->dropColumn('display_branch_info_in_invoice');
            $table->dropColumn('display_invoice_date_in_invoice');
            $table->dropColumn('display_created_by_in_invoice');
        });
    }
};
