<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLpContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lp_content', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('subject_id')->default(0)->nullable()->comment('id of subject');
            $table->integer('created_by')->default(0)->nullable()->comment('user id');
            $table->string('topic')->default('')->comment('topic of lesson plan');
            $table->string('lesson')->default('')->comment('lesson expected to student');
            $table->string('focus_goals')->default('')->comment('focus of the plan');
            $table->string('objective')->default('')->comment('objective');
            $table->integer('class_id')->default(0)->comment('id of class');
            $table->integer('status')->default(1)->comment('1=submitted,2=approved,3=rejected');
            $table->integer('ack_by')->default(0)->comment('id of user that approved the plan');
            $table->softDeletes();
            $table->timestamps();

            $table->index('subject_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lp_content');
    }
}
