<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\User;
use App\Playlist;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    /*public function index(){
        return view('auth/login');
    }*/

    public function login(Request $request){ //return $request->email;
        /*$email = 'chavin.pradana';
        $password = 'password123';
        $ldap_host = '172.18.8.10';//env('LDAP_HOST'); return $ldap_host;
        $ldap_dn = 'DC=mncgroup,DC=com';
        $ldap = ldap_connect($ldap_host);
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3) or die('Unable to set LDAP protocol version');
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
        $bind = @ldap_bind($ldap, $email, $password);
        return response()->json($bind);*/

        if(session_id() == ""){
            session_start();
        }
        
        $username = $request->email;
        $password = $request->password;

        $ldap = $this->check_ldap($username,$password);

        if($ldap === true){
            $mail = $username .'@mncgroup.com';
            //print_r($mail);
            //$_SESSION['email'] = $mail; //return 1;
            /*if(){

            }*/
            
            $playlist = new Playlist;
            try{
                $allPlaylist = $playlist->tree();
            }catch(Exception $e){
                //no parent category found
            } //return $allPlaylist;
            return view('home')->with('playlist', $allPlaylist);
            //return redirect('/dashboard')->with('playlist', $allPlaylist);
        }else{ //return 0;
            //echo "gagal koneksi ldap"; //return 0;
            //redirect(site_url(). "form_a");

            //$_SESSION['message'] = 'Wrong Username / Password';
            return redirect('/login');
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

    public function logout(Request $request){
        Auth::logout();
        return redirect('/login');
    }

    public function index()
    {
        $playlist = new Playlist;
        try{
            $allPlaylist = $playlist->tree();
        }catch(Exception $e){
            //no parent category found
        } //return $allPlaylist;
        return view('home')->with('playlist', $allPlaylist);
        //return view('home')->with('playlist', $playlist);
    }
}
