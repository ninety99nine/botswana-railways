<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id');

            /*  Ticket Details  */
            $table->string('full_name')->nullable();
            $table->string('passenger_type')->nullable();
            $table->string('identity_type')->nullable();
            $table->string('identity_no')->nullable();
            $table->string('reference_no')->nullable();
            $table->integer('booking_id')->nullable();

            /*  Timestamps  */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
