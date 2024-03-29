<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Socialite;
use Auth;


class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
            ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            ]);
    }

    public function redirectToProvider_facebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback_facebook()
    {
        $user = Socialite::driver('facebook')->user();

        $data=['name'=>$user->name,'email'=>$user->email, 'password'=>$user->token];

        $userDB=User::where('email',$user->email)->first();

        if(!is_null($userDB)){
            Auth::Login($userDB);
        }else{
            Auth::Login($this->create($data));
        }

        return redirect('/about');

        // $user->token;
    }

    public function redirectToProvider_google()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from google.
     *
     * @return Response
     */
    public function handleProviderCallback_google()
    {
        $user = Socialite::driver('google')->user();

        $data=['name'=>$user->name,'email'=>$user->email, 'password'=>$user->token];

        $userDB=User::where('email',$user->email)->first();

        if(!is_null($userDB)){
            Auth::Login($userDB);
        }else{
            Auth::Login($this->create($data));
        }

        return redirect('/about');

        // $user->token;
    }
    protected $redirectPath = '/about';
    protected $loginPath = '/';
}
