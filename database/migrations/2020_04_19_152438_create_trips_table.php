<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->bigIncrements('id');

            /*  Trip Details  */
            $table->tinyInteger('from_destination_id')->nullable();
            $table->tinyInteger('to_destination_id')->nullable();
            $table->tinyInteger('class_id')->nullable();
            $table->float('adult_price')->nullable();
            $table->float('senior_price')->nullable();
            $table->float('child_price')->nullable();
            $table->float('infant_price')->nullable();

            /*  Times */
            $table->time('leave_at', 0);
            $table->time('arrive_at', 0);

            $table->smallInteger('train_number')->default(0);
            $table->boolean('is_available')->default(1);

            $table->index(['from_destination_id', 'to_destination_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trips');
    }
}
