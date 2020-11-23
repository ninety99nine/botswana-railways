<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');

            /*  Booking (Leaving Details)  */
            $table->string('msisdn')->nullable();
            $table->string('class_id')->nullable();
            $table->string('accommodation_id')->nullable();
            $table->string('start_destination_id')->nullable();
            $table->string('end_destination_id')->nullable();
            $table->timestampTz('leave_datetime')->nullable();
            $table->timestampTz('arrive_datetime')->nullable();
            $table->smallInteger('train_number')->nullable();

            /*  Booking (Return Details)  */
            $table->boolean('wants_to_return')->default(0);
            $table->string('return_class_id')->nullable();
            $table->string('return_accommodation_id')->nullable();
            $table->string('return_start_destination_id')->nullable();
            $table->string('return_end_destination_id')->nullable();
            $table->timestampTz('return_leave_datetime')->nullable();
            $table->timestampTz('return_arrive_datetime')->nullable();
            $table->smallInteger('return_train_number')->nullable();

            /*  Trip Costs  */
            $table->float('payment_amount')->nullable();

            /*  Passenger Details  */
            $table->smallInteger('number_of_passengers')->default(0);
            $table->smallInteger('number_of_adults')->default(0);
            $table->smallInteger('number_of_seniors')->default(0);
            $table->smallInteger('number_of_children')->default(0);
            $table->smallInteger('number_of_infants')->default(0);

            /*  Emergency Details   */
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();

            /*  Cancellation Details  */
            $table->boolean('is_cancelled')->default(0);

            /*  Timestamps  */
            $table->timestamps();

            $table->index('msisdn');

            $table->index(['start_destination_id', 'end_destination_id', 'leave_datetime']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
