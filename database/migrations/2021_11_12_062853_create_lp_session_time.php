<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLpSessionTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lp_session_time', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('class_id')->default(1)->comment('class_id');
            $table->integer('year')->default('2021')->comment('year of session');
            $table->integer('subject_id')->default(0)->comment('subject id');
            $table->string('day_of_week')->default('isnin');
            $table->string('slot_number')->default('0')->comment('number of slot');;
            $table->string('start_time')->default('0800')->comment('start slot');
            $table->string('end_time')->default('1700')->comment('end slot');
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
        Schema::dropIfExists('lp_session_time');
    }
}
