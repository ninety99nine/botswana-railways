<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTravelClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('travel_classes', function (Blueprint $table) {
            $table->bigIncrements('id');

            /*  Classphp artisan make:model Flight Details  */
            $table->string('name')->nullable();
            $table->boolean('is_available')->default(1);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('travel_classes');
    }
}
