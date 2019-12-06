<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Auth;
use Mail;
use App\Mail\SendMail;
use App\Http\Controllers\Controller;

class SendMailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        //$this->middleware('auth:api');
    }

    public function mail(){ //return 'fired';
        //Mail::to(auth('api')->user()->email)->send(new SendMail());
        return Mail::to('chavin.pradana@mncgroup.com')->send(new SendMail());
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*$data = array(
            'name' => $request->name,
            'title' => $request->title,
            'body' => $request->body,
        );*/
        //return Mail::to(auth('api')->user()->email)->send(new SendMail());
        /*return Mail::to('lita.pertiwi@mncgroup.com')
            ->send(new SendMail($data));*/
        //Mail::to('chavinpradana@gmail.com')->send(new SendMail());

        try{
            Mail::send('mail/mail_template', ['nama' => $request->name, 'pesan' => $request->body], function ($message) use ($request){
                $message->subject($request->title);
                $message->from('donotreply@kiddy.com', 'Kiddy');
                //$message->to($request->email);
                $message->to('chavinpradana@gmail.com');
            });
            return back()->with('alert-success', 'Berhasil Kirim Email');
        }catch (Exception $e){
            return response ([
                'status' => false, 
                'errors' => $e->getMessage()
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }
}
