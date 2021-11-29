<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DateTime;
Use Alert;
Use Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
//get user profle
        $user_profile = DB::table('user_profile')
            ->where('user_id',$user->id)
            ->first();
        $class_id = $user_profile->class_id;

//        alert()->success('Title','Lorem Lorem Lorem');
//        Alert::success('Success Title', 'Success Message');
//        toast("Welcome $user->name !",'success');
//        alert()->image('Image Title!','Image Description',$user->icon,'200','100','aaaa');

        $user_info = [
            'user_profile' => $user_profile,
            'user_details' => $user
        ];

        Session::put('user_info', $user_info);

        if($user_profile->type == 1){
            //is student
            //show time table
            $year = date('Y');
            $ddate = date("Y-m-d");
            $date = new DateTime($ddate);
            $week = $date->format("W");
            return self::student_timetable($class_id,$year,$week);
        }

        if($user_profile->type == 2){
            //is teacher
            //show menu later
            return view('diah');
        }

    }

    public function home(){
        $user_info = Session::get('user_info');
        $user_profile = $user_info['user_profile'];
        $user_details = $user_info['user_details'];
        $class_id = $user_profile->class_id;
        $year = date('Y');
        $ddate = date("Y-m-d");
        $date = new DateTime($ddate);
        $week = $date->format("W");
        return self::student_timetable($class_id,$year,$week);
    }

    public static function student_timetable($class_id,$year,$week){

//        echo "Weeknummer: $week";
        $this_week['week_number'] = $week;
//        echo date("Y-m-d H:i:s");

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

        $class_info = DB::table('class_info')
            ->where('id',$class_id)
            ->first();

        $slot_info = DB::table('lp_session_time')
            ->where('class_id',$class_id)
            ->get();


//        dd($this_week,$class_info,$slot_info);


//        $time_slot = json_decode($class_info->time_slot,true);

        $user_info = Session::get('user_info');
        $user = $user_info['user_details'];
        return view('timetable')
            ->with('this_week',$this_week)
            ->with('year',$year)
            ->with('week',$week)
            ->with('user',$user)
            ->with('slot_info',$slot_info)
            ->with('class_info',$class_info);

    }
    public static function get_time_slot_info($class_id,$slot_number,$year){
        $class_info = DB::table('lp_session_time')
            ->where('class_id',$class_id)
            ->where('year',$year)
            ->where('slot_number',$slot_number)
            ->first();
        $time_slot_info = $class_info->start_time . " - " . $class_info->end_time;
        return $time_slot_info;
    }

    public static function get_subject_slot_info($class_id,$slot_number,$year,$day,$week){
        $class_info = DB::table('lp_session_time')
            ->leftJoin('lp_subject','lp_session_time.subject_id','=','lp_subject.id')
            ->where('class_id',$class_id)
            ->where('year',$year)
            ->where('slot_number',$slot_number)
            ->where('day_of_week',$day)
            ->select('lp_session_time.subject_id','lp_subject.name as subject_name','lp_subject.icon','lp_session_time.id')
            ->first();
//        dd($class_info);
        if($class_info){
            $subject_name = $class_info->subject_name;
            $icon = $class_info->icon;
        }
        else{
            $subject_name = 'none';
            $icon = "/none.png";
        }
        return view('timetable-subject')
            ->with('class_info',$class_info)
            ->with('icon',$icon)
            ->with('year',$year)
            ->with('week',$week)
            ->with('subject',$subject_name);
    }

    public static function show_day($day,$date){
//        $text = ucfirst($day)."<br>".$date;
        return view('timetable-day')
            ->with('date',$date)
            ->with('day',$day);
    }

}
