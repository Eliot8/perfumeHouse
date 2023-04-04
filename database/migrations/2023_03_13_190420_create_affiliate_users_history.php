<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffiliateUsersHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('affiliate_users_histories', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('affiliate_user_id')->constrained('affiliate_users');
        //     $table->foreignId('order_id')->constrained('orders');
        //     $table->decimal('commission');
        //     $table->decimal('balance');
        //     $table->decimal('pending_balance');
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
        // Schema::dropIfExists('affiliate_users_histories');
    }
}
