<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Support\Facades\DB;
use Socialite;
use Auth;
use Exception;
use App\User;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }



    public function redirectToGoogle()

    {

        return Socialite::driver('google')->redirect();

    }

    public function handleGoogleCallback()

    {

        $user = Socialite::driver('google')->user();
        $finduser = User::where('email', $user->email)->first();

        if($finduser){
            Auth::login($finduser);
//            dd('still not register',$user);
            DB::table('users')
                ->where('email',$user->email)
                ->update([
                    'name' => $user->name,
                    'google_id'=> $user->id,
                    'updated_at'=> date('Y-m-d H:i:s'),
                    'token'=> $user->token,
                    'icon'=>$user->avatar,
                ]);

            return redirect('/home');

        }else{
            Session::flash('message', "Tiada aktif rekod untuk $user->email dalam sistem ULTD");
            Session::flash('alert-class', 'alert-danger');
            return redirect('/login');
            #return redirect()->back();
        }

    }

    public function redirectToFacebook()

    {

        return Socialite::driver('facebook')->redirect();

    }

    public function handleFacebookCallback()

    {

        $user = Socialite::driver('facebook')->user();
        dd($user);
        $finduser = User::where('email', $user->email)->first();

        if($finduser){
            Auth::login($finduser);
//            dd('still not register',$user);
            DB::table('users')
                ->where('email',$user->email)
                ->update([
                    'name' => $user->name,
                    'google_id'=> $user->id,
                    'updated_at'=> date('Y-m-d H:i:s'),
                    'token'=> $user->token,
                    'icon'=>$user->avatar,
                ]);

            return redirect('/home');

        }else{
            dd('user not found');
//            return redirect()->back();
//            return redirect('/login');
            $newUser = User::updateOrCreate([
                'name' => $user->name,
                'email' => $user->email,
                'google_id'=> $user->id,
                'updated_at'=> date('Y-m-d H:i:s'),
                'token'=> $user->token,
                'icon'=>$user->avatar,
                'password'=>'',
            ]);
            Auth::login($newUser);
            return redirect('/home');
            #return redirect()->back();
        }

    }


}
