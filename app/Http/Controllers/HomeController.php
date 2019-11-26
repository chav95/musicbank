<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
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
