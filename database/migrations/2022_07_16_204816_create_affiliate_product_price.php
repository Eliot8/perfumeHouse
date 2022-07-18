<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffiliateProductPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//         Schema::create('affiliate_product_price', function (Blueprint $table) {
//             $table->id();
//             $table->integer('affiliate_user_id')->unsigned();
//             $table->integer('product_id')->unsigned();
            
//             $table->foreign('affiliate_user_id')->references('id')->on('affiliate_users')->onUpdate('cascade')->onDelete('cascade');
//             $table->foreign('product_id')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');

//             // $table->foreignId('affiliate_user_id')->constrained('affiliate_users')->onUpdate('cascade')->onDelete('cascade');
//             // $table->foreignId('product_id')->constrained('products')->onUpdate('cascade')->onDelete('cascade');
//             $table->decimal('price', 2);
//             $table->timestamps();
//         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//         Schema::dropIfExists('affiliate_product_price');
    }
}
