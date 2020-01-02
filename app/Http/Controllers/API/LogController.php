<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Log;

class LogController extends Controller
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
        return DB::table('musics')
            ->select('users.name', 'logs.created_at', 'musics.judul')
            ->rightJoin('logs', 'logs.music_id', '=', 'musics.id')
            ->leftJoin('users', 'logs.user_id', '=', 'users.id')
            ->paginate(10);
        //return Log::latest()->paginate(10);
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
        $log = new Log;
        $log->music_id = $request->music_id;
        $log->action = $request->action;
        $log->filename = $request->filename;
        $log->user_id = auth('api')->user()->id;
        $log->save();

        return response()->json(array('success' => true, 'last_insert_id' => $log->id), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if($id == 'getMostDownloadThisMonth'){
            $date = Carbon::today();
            $month_string = $date->format('M Y');
            $m = $date->format('n');
            $y = $date->format('Y');
            $year_month = $y.'-'.$m; //->where('created_at', 'LIKE', "{$year_month}%")
            $data = Log::select('music_id', DB::raw('COUNT(music_id) as download'))->with('music')
                ->groupBy('music_id')->orderByRaw('COUNT(*) DESC')->limit(5)->get();

            $result = $data;
        }
        return $result;
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
