<?php namespace AWME\Parking\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateTillsTable extends Migration
{

    public function up()
    {
        Schema::create('awme_parking_tills', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('action');
            $table->string('tiket')->nullable();
            $table->string('billing');
            
            $table->string('seller')->nullable();

            $table->decimal('subtotal', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            
            $table->longText('description');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('awme_parking_tills');
    }

}
