<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHuntUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hunt_user_details', function ($collection) {
            $collection->index('hunt_user_id');
            $collection->index('game_id');
            $collection->index('game_variation_id');
            $collection->geospatial('location.coordinates', '2dsphere');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hunt_user_details');
    }
}
