<?php namespace AWME\Parking\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateClientsTable extends Migration
{

    public function up()
    {
        Schema::create('awme_parking_clients', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('name');
            $table->string('lastname');
            $table->string('fullname');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('color')->nullable();
            $table->string('registration')->nullable();
            
            $table->integer('parking_id')->unsigned();

            $table->string('billing')->nullable();
            $table->timestamp('expiration')->nullable();
            
            $table->longText('description')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('awme_parking_clients');
    }

}
