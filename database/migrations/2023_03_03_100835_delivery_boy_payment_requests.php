<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeliveryBoyPaymentRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('delivery_boy_payment_requests', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('code')->nullable();
        //     $table->foriengId('delivery_man_id')->constrainde('delegates');
        //     $table->date('date_request');
        //     $table->json('attached_pieces')->nullable();
        //     $table->decimal('amount');
        //     $table->text('comment');
        //     $table->string('status', 50);
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
        //
    }
}
