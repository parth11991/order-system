<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFilesDetailesToOrderFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders_files', function (Blueprint $table) {
            $table->string('originalname')->nullable();
            $table->string('size')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders_files', function (Blueprint $table) {
            $table->dropColumn('originalname');
            $table->dropColumn('size');
        });
    }
}
