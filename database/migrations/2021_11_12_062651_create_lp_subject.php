<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateLpSubject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lp_subject', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->default('')->comment('subject name');
            $table->softDeletes();
            $table->timestamps();
        });
        $subject = array(
            "Bahasa Melayu",
            "Bahasa Inggeris",
            "Bahasa Cina",
            "Matematik",
            "Pendidikan Islam",
            "Sains",
            "Pendidikan Jasmani",
            "Sejarah",
            "Pendidikan Seni",
            "Reka Bentuk & Teknologi",
            "Tambahan",
        );
        for($a=0;$a<count($subject);$a++){
            DB::table('lp_subject')->insert([
                'name' => $subject[$a],
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
        }


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lp_subject');
    }
}
