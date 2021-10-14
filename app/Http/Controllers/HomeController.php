<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        return view('home2');
    }
    public function diah(){
//        echo "hhh";
        return view('diah');
    }
    public  function  hadi(){
        echo "haha";
        for($a=0;$a<10;$a++){

            $b = self::kira_nombor($a);
            echo "bubu ". $b;
            echo "<br>";
        }
    }

    public static function kira_nombor($avv){
        $number = 5 * $avv;
        return $number;
    }
}
