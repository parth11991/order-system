<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemDimensions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_has_items', function (Blueprint $table) {
            $table->decimal('product_weight', 9, 2)->default(0)->nullable();
            $table->decimal('product_width', 9, 2)->default(0)->nullable();
            $table->decimal('product_length', 9, 2)->default(0)->nullable();
            $table->decimal('product_depth', 9, 2)->default(0)->nullable();
            $table->decimal('box_inner_quantity', 9, 2)->default(0)->nullable();
            $table->decimal('box_outer_quantity', 9, 2)->default(0)->nullable();
            $table->decimal('box_weight_net_kg', 9, 2)->default(0)->nullable();
            $table->decimal('box_weight_gross_kg', 9, 2)->default(0)->nullable();
            $table->decimal('box_width_cm', 9, 2)->default(0)->nullable();
            $table->decimal('box_length_cm', 9, 2)->default(0)->nullable();
            $table->decimal('box_depth_cm', 9, 2)->default(0)->nullable();
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
            $table->dropColumn('product_weight');
            $table->dropColumn('product_width');
            $table->dropColumn('product_length');
            $table->dropColumn('product_depth');
            $table->dropColumn('box_inner_quantity');
            $table->dropColumn('box_outer_quantity');
            $table->dropColumn('box_weight_net_kg');
            $table->dropColumn('box_weight_gross_kg');
            $table->dropColumn('box_width_cm');
            $table->dropColumn('box_length_cm');
            $table->dropColumn('box_depth_cm');
        });
    }
}
