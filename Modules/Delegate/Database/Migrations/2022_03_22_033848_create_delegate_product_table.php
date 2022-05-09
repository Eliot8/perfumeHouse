<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDelegateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('delegate_product', function (Blueprint $table) {
        //     $table->increments('id');
        //     $table->integer('stock')->default(0);
        //     $table->integer('delegate_id')->unsigned();
        //     $table->integer('product_id');
        //     $table->foreign('delegate_id')->references('id')->on('delegates')->onUpdate('cascade')->onDelete('cascade');
        //     $table->foreign('product_id')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('delegate_product');
    }
}
