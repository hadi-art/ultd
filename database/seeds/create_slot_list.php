<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class create_slot_list extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('lp_session_time')->truncate();
        $class = DB::table('class_info')
            ->get();

        //week type 1=start monday, 2=start sunday
        $week_type = env('WEEK_TYPE',1);
        if($week_type == 2){
            $dt_min2 = new DateTime("sunday last week");
            $this_week[0]['date']= $dt_min2->format('Y-m-d');
            $this_week[0]['day']= 'ahad';
            $this_week[1]['date']= $dt_min2->modify('+1 day')->format('Y-m-d');
            $this_week[1]['day']= 'isnin';
            $this_week[2]['date']= $dt_min2->modify('+1 day')->format('Y-m-d');
            $this_week[2]['day']= 'selasa';
            $this_week[3]['date']= $dt_min2->modify('+1 day')->format('Y-m-d');
            $this_week[3]['day']= 'rabu';
            $this_week[4]['date']= $dt_min2->modify('+1 day')->format('Y-m-d');
            $this_week[4]['day']= 'khamis';
            $this_week[5]['date']= $dt_min2->modify('+1 day')->format('Y-m-d');
            $this_week[5]['day']= 'jumaat';
            $this_week[6]['date']= $dt_min2->modify('+1 day')->format('Y-m-d');
            $this_week[6]['day']= 'sabtu';
        }
        else{
            $dt_min = new DateTime("monday this week");
            $this_week[0]['date']= $dt_min->format('Y-m-d');
            $this_week[0]['day']= 'isnin';
            $this_week[1]['date']= $dt_min->modify('+1 day')->format('Y-m-d');
            $this_week[1]['day']= 'selasa';
            $this_week[2]['date']= $dt_min->modify('+1 day')->format('Y-m-d');
            $this_week[2]['day']= 'rabu';
            $this_week[3]['date']= $dt_min->modify('+1 day')->format('Y-m-d');
            $this_week[3]['day']= 'khamis';
            $this_week[4]['date']= $dt_min->modify('+1 day')->format('Y-m-d');
            $this_week[4]['day']= 'jumaat';
            $this_week[5]['date']= $dt_min->modify('+1 day')->format('Y-m-d');
            $this_week[5]['day']= 'sabtu';
            $this_week[6]['date']= $dt_min->modify('+1 day')->format('Y-m-d');
            $this_week[6]['day']= 'ahad';
        }

        for($c=0;$c<5;$c++){

            $day = $this_week[$c]['day'];
            for($a=0;$a<count($class);$a++){

                //morning timeslot
                $time_slot = [
                    [
                        'number' => 0,
                        'start_time' => '08:00',
                        'end_time' => '08:30'
                    ],
                    [
                        'number' => 1,
                        'start_time' => '08:30',
                        'end_time' => '09:00'
                    ],
                    [
                        'number' => 2,
                        'start_time' => '09:00',
                        'end_time' => '09:30'
                    ],
                    [
                        'number' => 3,
                        'start_time' => '09:30',
                        'end_time' => '10:00'
                    ],
                    [
                        'number' => 4,
                        'start_time' => '10:00',
                        'end_time' => '10:30'
                    ],
                    [
                        'number' => 5,
                        'start_time' => '10:30',
                        'end_time' => '11:00'
                    ],
                    [
                        'number' => 6,
                        'start_time' => '11:00',
                        'end_time' => '11:30'
                    ],
                    [
                        'number' => 7,
                        'start_time' => '11:30',
                        'end_time' => '12:00'
                    ],
                    [
                        'number' => 8,
                        'start_time' => '12:00',
                        'end_time' => '12:30'
                    ],
                    [
                        'number' => 9,
                        'start_time' => '12:30',
                        'end_time' => '13:00'
                    ]

                ];
//            dd($time_slot);

                for($b=0;$b<count($time_slot);$b++){
                    $slot_number = $time_slot[$b]['number'];
                    if($slot_number == 4){
                        $subject_id = 1;
                    }
                    else{
                        $subject_id = (rand(2,12));
                    }

                    DB::table('lp_session_time')->insert([
                        'year' => date('Y'),
                        'slot_number' => $time_slot[$b]['number'],
                        'day_of_week' => $day,
                        'subject_id' => $subject_id,
                        'start_time' => $time_slot[$b]['start_time'],
                        'end_time' => $time_slot[$b]['end_time'],
                        'class_id' => $class[$a]->id,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s")
                    ]);
                }

            }


        }





    }
}
