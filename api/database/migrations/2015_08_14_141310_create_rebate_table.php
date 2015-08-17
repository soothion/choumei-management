<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRebaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rebate', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('salon_id');
            $table->string('author',20);
            $table->string('sn',15)->unique();
            $table->decimal('amount', 10, 2);
            $table->tinyInteger('status');
            $table->timestamp('start_at');
            $table->timestamp('end_at');
            $table->timestamp('confirm_at');
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
        //
    }
}
