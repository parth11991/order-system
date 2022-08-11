<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('item_id');
            $table->string('sku');
            $table->string('customer_sku');
            
            $table->string('item_title');
            $table->decimal('price', 9, 2)->nullable();
            $table->date('due_date')->nullable();
            $table->string('image');
            $table->integer('qty');
            $table->enum('status', ['0','1','2','3','4','5'])->default('0')->comment('0 = new order, 1 = confirmed, 2 = shipped, 3 = mark as received, 4 = Quote, 5 = Approved');
            $table->unsignedBigInteger('supplier_id')->unsigned()->index();
            $table->unsignedBigInteger('created_by')->index();
            $table->unsignedBigInteger('updated_by')->index();
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('orders');
        Schema::enableForeignKeyConstraints();
    }
}
