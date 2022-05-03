<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('new_price', 9, 2)->default(0)->nullable();
            $table->decimal('old_price', 9, 2)->default(0)->nullable();
            $table->string('currency')->default('GBP');
            $table->string('old_price_currency')->default('GBP');
            $table->string('currency')->default('GBP');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('new_price');
            $table->dropColumn('old_price');
            $table->dropColumn('currency');
            $table->dropColumn('old_price_currency');
            $table->dropColumn('new_price_currency');
        });
    }
}
