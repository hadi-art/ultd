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
            $table->integer('status')->default(1)->comment('0=not a subject is for break time,1=subject');
            $table->string('icon')->default('/none.png')->comment('icon picture');
            $table->softDeletes();
            $table->timestamps();
        });

        $link = '/images/ultdtimetableicons';
        $subject = [
            ['name'=> 'Rehat', 'icon' => "$link/break.png"],
            ['name'=> 'Bahasa Melayu', 'icon' => "$link/bahasamelayu.png"],
            ['name'=> 'Bahasa Inggeris', 'icon' => "$link/bahasainggeris.png"],
            ['name'=> 'Bahasa Cina', 'icon' => "$link/bahasacina.png"],
            ['name'=> 'Bahasa Tamil', 'icon' => "$link/bahasatamil.png"],
            ['name'=> 'Sains', 'icon' => "$link/sains.png"],
            ['name'=> 'Matematik', 'icon' => "$link/matematik.png"],
            ['name'=> 'Pendidikan Islam', 'icon' => "$link/pendislam.png"],
            ['name'=> 'Pendidikan Jasmani', 'icon' => "$link/pendjasmani.png"],
            ['name'=> 'Sejarah', 'icon' => "$link/sejarah.png"],
            ['name'=> 'Pendidikan Seni', 'icon' => "$link/pendseni.png"],
            ['name'=> 'Reka Bentuk & Teknologi', 'icon' => "$link/rekatek.png"],
            ['name'=> 'Pendidikan Moral', 'icon' => "$link/pendmoral.png"],
        ];


        for($a=0;$a<count($subject);$a++){
            if($subject[$a]['name'] == 'Rehat'){
                $status = 0;
            }
            else{
                $status = 1;
            }
            DB::table('lp_subject')->insert([
                'name' => $subject[$a]['name'],
                'status' => $status,
                'icon' => $subject[$a]['icon'],
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
//            dd($subject);
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
