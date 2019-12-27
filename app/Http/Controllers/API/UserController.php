<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\User;
use Auth;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::select('id', 'name', 'email', 'user_type', 'hak_akses', 'created_at')
            ->with('userType')->with('hakAkses')
            ->where('status', '=', '1')->orderBy('name', 'asc')->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->act){ //return $request;
            if($request->act == 'deactivate_user'){
                return User::where('id', $id)->update(['status' => 0]);
            }else if($request->act == 'edit_user'){
                $this->validate($request, [
                    'name' => 'required|string|max:191',
                    'email' => 'required|string|email|max:191|unique:users,email,'.$request->selectedUserId,
                    'user_type' => 'required|integer',
                    'hak_akses' => 'required|integer'
                ]);

                /*$user = User::find($request->selectedUserId);
                return $user->email;*/

                return User::where('id', $request->selectedUserId)
                    ->update([
                        'name' => $request->name,
                        'email' => $request->email,
                        'user_type' => $request->user_type,
                        'hak_akses' => $request->hak_akses,
                    ]);
            }else if($request->act == 'new_user'){
                $this->validate($request, [
                    'name' => 'required|string|max:191',
                    'email' => 'required|string|email|max:191|unique:users',
                    'user_type' => 'required|integer',
                    'hak_akses' => 'required|integer'
                ]);

                return User::create([
                    'name' => $request['name'],
                    'email' => $request['email'],
                    'password' => Hash::make('123456'),
                    'user_type' => $request['user_type'],
                    'hak_akses' => $request['hak_akses'],
                ]);
            }
        }else{
            //default register function from laravel
            $this->validate($request, [
                'name' => 'required|string|max:191',
                'email' => 'required|string|email|max:191|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            return User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
                'api_token' => Str::random(60),
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if($id == 'getUserLogin'){ 
            return auth('api')->user();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return User::where('id', $id)->update(['status' => -1]);
    }
}
