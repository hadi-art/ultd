<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateClassInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_info', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->default('Amanah');
            $table->text('time_slot')->comment('time session slot');
            $table->string('description')->default('');
            $table->timestamps();
        });
        //morning session
        $morning_session =array(
            '0' => '08:00 - 08.30',
            '1' => '08:30 - 09.00',
            '2' => '09:00 - 09.30',
            '3' => '09:30 - 10.00',
            '4' => '10:00 - 10.30',
            '5' => '10:30 - 11.00',
            '6' => '11:00 - 11.30',
            '7' => '11:30 - 12.00',
            '8' => '12:00 - 12.30',
            '9' => '12:30 - 13.00',
        );
        $json_morning_session = json_encode($morning_session);

        $class = array(
            "1 Amanah",
            "1 Bistari",
            "1 Cekal",
            "1 Dedikasi",
        );
        for($a=0;$a<count($class);$a++){
            DB::table('class_info')->insert([
                'name' => $class[$a],
                'time_slot' => $json_morning_session,
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
        Schema::dropIfExists('class_info');
    }
}
