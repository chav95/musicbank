<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Illuminate\Http\Request;

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
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }        

    public function logout(Request $request) {
        Auth::logout();
        return redirect('/login');
    }

    public function login(Request $request){
        $this->validateLogin($request);

        if($this->hasTooManyLoginAttempts($request)){
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }
        
        $username = str_replace('@mncgroup.com', '', $request->email);
        $password = $request->password;

        $ldap = $this->check_ldap($username,$password);
        //$request->email .= '@mncgroup.com';

        if($ldap === true){ //return $request->email;
            if($this->attemptLogin($request)){
                return $this->sendLoginResponse($request);
            }else{
                $this->incrementLoginAttempts($request);
                return $this->sendFailedLoginResponse($request);
            }
        }else{
            $this->incrementLoginAttempts($request);
            return $this->sendFailedLoginResponse($request);
        }
    }

    function check_ldap($username,$password){
        $ldap_ip = '172.18.8.10'; 
        $ldap_port=389;
        $ldap_domain ="@mncgroup.com";
        
        $ds = ldap_connect($ldap_ip, $ldap_port) or die("Could not connect to $ldaphost");

        if($ds){
            try{
                $usercode = $username.$ldap_domain;
                $ldapbind = @ldap_bind($ds, $usercode, $password); 
                // $ldap_dn = 'OU=MNCMedia,DC=mncgroup,DC=com';
                // $results = ldap_search($ds, $ldap_dn, "(mail=$usercode)");
                // $entries = ldap_get_entries($ds, $results);

                if (@$ldapbind){
                    return true;                                         
                }
                else{
                    return false;

                    echo "Username dan Password Salah";
                }
            }catch(Exception $e){
                return false;
            }                  
        } 
    }
}
