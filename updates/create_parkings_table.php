<?php namespace AWME\Parking\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateParkingsTable extends Migration
{

    public function up()
    {
        Schema::create('awme_parking_parkings', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('tiket');
            $table->integer('client_id')->nullable();
            $table->string('fullname')->nullable();
            $table->string('billing')->nullable();
            $table->integer('garage_id');
            
            $table->timestamp('checkin');
            $table->timestamp('checkout');

            $table->longText('description');
            $table->string('options');
            $table->decimal('total', 10, 2)->default(0)->nullable();
            $table->string('status');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('awme_parking_parkings');
    }

}
