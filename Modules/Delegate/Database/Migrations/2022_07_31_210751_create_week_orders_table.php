<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeekOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('week_orders', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('delivery_man_id')->constrained();
            $table->integer('delivery_man_id')->unsigned();
            $table->date('week_start');
            $table->date('week_end');
            $table->decimal('system_earnings', 8);
            $table->decimal('personal_earnings', 8);
            $table->boolean('payement_request')->default(0);
            $table->foreign('delivery_man_id')->references('id')->on('delegates')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('week_orders');
    }
}
