<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLpSubject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lp_subject', function (Blueprint $table) {
            $table->string('icon')->after('name')->default('/none.png')->comment('icon picture');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lp_subject', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
}