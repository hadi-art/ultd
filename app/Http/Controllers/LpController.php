<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;

class LpController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $user_profile = Session::get('user_info');
//        dd($user_profile);
        $user = $user_profile['user_details'];
//        dd($user_profile);

        return view('lp/home')
//            ->with('this_week',$this_week)
            ->with('user',$user)
//            ->with('slot_info',$slot_info)
            ->with('user_profile',$user_profile);


    }

    public function get_lp_slot_info(Request $request){

    }
}
