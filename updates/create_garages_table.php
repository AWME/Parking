<?php namespace AWME\Parking\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateGaragesTable extends Migration
{

    public function up()
    {
        Schema::create('awme_parking_garages', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();

            $table->string('name')->nullable();
            $table->string('color')->nullable();
            $table->string('status')->nullable();
            $table->longText('description')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('awme_parking_garages');
    }

}
