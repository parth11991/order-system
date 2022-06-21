<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupplierHasItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_has_items', function (Blueprint $table) {
            $table->string('supplier_code')->nullable();
            $table->string('supplier_barcode')->nullable();
            $table->time('lead_time')->nullable();
            $table->integer('supplier_price')->default(0)->nullable();
            $table->string('supplier_currency')->default('GBP');
            $table->integer('min_order_quantity')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_has_items', function (Blueprint $table) {
            $table->dropColumn('supplier_code');
            $table->dropColumn('supplier_barcode');
            $table->dropColumn('lead_time');
            $table->dropColumn('supplier_price');
            $table->dropColumn('supplier_currency');
            $table->dropColumn('min_order_quantity');
        });
    }
}
