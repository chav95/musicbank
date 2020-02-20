<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;
use Response;
use File;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Playlist;
use App\Music;
use App\MusicPlaylist;
use App\Log;
use Carbon\Carbon;
use Auth;

class MusicController extends Controller
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
    public function index(){
        $playlist = new Playlist;
        try{
            $allPlaylist = $playlist->tree();
        }catch(Exception $e){
            //no parent category found
        }
        
        return $allPlaylist;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request){
        $playlist = $request->playlist;
        if($request->hasFile('file_name')){
            $s3Client = new S3Client([
                'region' => 'us-west-2',
                'version' => '2006-03-01',
            ]);

            foreach($request->file_name as $item){
                $originalFilename = trim($item->getClientOriginalName());
                $extension = $item->getClientOriginalExtension();
                $filenameOnly = pathinfo($originalFilename, PATHINFO_FILENAME);
                $filename = str_slug($filenameOnly).'.'.$extension;

                if(Storage::disk('ftp')->exists($originalFilename)){
                    return response()->json(array('error' => 'File already exist'), 500);
                }else{
                    $getID3 = new \getID3;
                    return $metadata = self::convert_latin1_to_utf8($getID3->analyze($item));

                    if(Storage::disk('ftp')->put($originalFilename, fopen($item, 'r+'))){
                        $music = new Music;
                        $music->judul = $filenameOnly;
                        $music->filename = $originalFilename; //$filename;
                        $music->uploaded_by = auth('api')->user()->id;
                        $music->filetype = $extension;
                        //$music->filesize = $item->getSize();

                        if(isset($metadata['tags']['id3v2']['artist'])){
                            $music->artis = json_encode($metadata['tags']['id3v2']['artist']);
                        }
                        if(isset($metadata['tags']['id3v2']['album'])){
                            $music->album = json_encode($metadata['tags']['id3v2']['album']);
                        }
                        if(isset($metadata['tags']['id3v2']['genre'])){
                            $music->genre = json_encode($metadata['tags']['id3v2']['genre']);
                        }
                        if(isset($metadata['tags']['id3v2']['composer'])){
                            $music->composer = json_encode($metadata['tags']['id3v2']['composer']);
                        }
                        if(isset($metadata['tags']['id3v2']['year'])){
                            $music->tahun = $metadata['tags']['id3v2']['year'][0];
                        }
                        $music->filesize = $metadata['filesize'];
                        $music->durasi = $metadata['playtime_seconds'];
                        $music->bit_rate = $metadata['audio']['bitrate'];
                        $music->sample_rate = $metadata['audio']['sample_rate']; //return json_encode($music);

                        $music->save();

                        $music_id = $music->id;
                        $playlist_arr = explode(',', $request->playlist);
                        foreach($playlist_arr as $id){
                            $music_playlist = new MusicPlaylist;
                            $music_playlist->music_id = $music_id;
                            $music_playlist->playlist_id = $id;
                            $music_playlist->save();
                        }
                        
                        $log = new Log;
                        $log->item_id = $music->id;
                        $log->action = 'upload music';
                        $log->item_name = $originalFilename;
                        $log->user_id = auth('api')->user()->id;
                        $log->save();
                    }else{
                        return response()->json(array('error' => 'Failed to Upload'), 500);
                    }
                }
            }
            return response()->json(array('success' => true), 200);
        }else if($request->act){ //return $request;
            if($request->act == 'remove_from_playlist'){                
                return MusicPlaylist::where(['music_id' => $request->music_id, 'playlist_id' => $request->playlist_id])->delete();
            }
            return $request->act;
        }

        return $request->file_name;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $lookback = 6; //jumlah bulan utk data dashboard

        if($id == 'plain_playlist'){ //return $id;
            $result = Playlist::where('status', '1')->orderBy('nama_playlist', 'asc')->get();
        }else if($id == 'totalMusicList'){
            $result = Music::where('status', 1)->count();
        }else if(strpos($id, 'totalPlaylist') !== false){
            $index = strpos($id, '_');
            $playlist = substr($id, ($index+1));
            $result = collect(self::flatten(Playlist::where(['status' => 1, 'id' => $playlist])
                ->with('music')->with('allChildrenContent')->get(), ''))->count();
        }else if($id == 'getMusicListByUploadDate'){
            $result = Music::latest()->where('status', '=', '1')->paginate(10);
        }else if($id == 'getMusicListByTitle'){
            $result = Music::orderBy('judul', 'ASC')->where('status', '=', '1')->paginate(10);
        }else if(strpos($id, 'playlist') !== false){
            //get playlist index
            $index = strpos($id, '-');
            $playlist = substr($id, ($index+1));

            //get sorting param
            preg_match('~_(.*?)@~', $id, $param); //get result in $param[1]
            preg_match('~@(.*?)-~', $id, $ascdesc); //return $ascdesc;
            
            $result = self::flatten(Playlist::where('id', $playlist)->with('music')->with('allChildrenContent')->get(), '');
            $result = collect($result)->paginate(10);
        }else if(strpos($id, 'searchMusic') !== false){ return $id;
            $params = str_replace('searchMusic?', '', $id);
            return $params;
        }else if($id == 'getUploadedMusicPerMonth'){
            $result = [];
            
            for($i=0; $i<$lookback; $i++){
                $date = Carbon::today()->subMonthsNoOverflow($i);
                $month_string = $date->format('M Y');
                $m = $date->format('m'); //return $m;
                $y = $date->format('Y'); //return $y;
                $year_month = $y.'-'.$m; //return $year_month;
                $data = MusicPlaylist::where('created_at', 'LIKE', "{$year_month}%")->count();

                $temp_array = array(
                    'label' => $month_string,
                    'data' => $data,
                );
                
                array_push($result, $temp_array);
            }
        }else if($id == 'getStorageUsagePerMonth'){
            $result = [];

            for($i=0; $i<$lookback; $i++){
                $date = Carbon::today()->subMonthsNoOverflow($i);
                $month_string = $date->format('M Y');
                $m = $date->format('m'); //return $m;
                $y = $date->format('Y'); //return $y;
                $year_month = $y.'-'.$m; //return $year_month;
                $data = round(Music::where('created_at', 'LIKE', "{$year_month}%")->sum('filesize')/pow(1024, 3), 2);

                $temp_array = array(
                    'label' => $month_string,
                    'data' => $data,
                );
                
                array_push($result, $temp_array);
            }
        }

        return $result;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        return $id;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        if(is_numeric($id)){ // delete music from database & ftp storage
            return response()->json(array(
                'Storage' => Storage::disk('ftp')->delete(
                    str_replace('http://172.18.11.32:8082/aud_uploads/', '',
                        str_replace("%20", " ", 
                            str_replace("%23", "#", 
                                Music::where('id', $id)->first()->filename
                            )
                        )
                    )
                ),
                'MusicPlaylist' => MusicPlaylist::where('music_id', $id)->delete(),
                'Music' => Music::where('id', $id)->delete(),
            ), 200);
        }else{ // delete music from public folder after being downloaded
            return response()->json(array('delete temp' => Storage::disk('public')->delete($id)), 200);
        }
        
    }

    public function download($filename){
        $getFile = Storage::disk('ftp')->get($filename);
        Storage::disk('public')->put($filename, $getFile);
        //return url('/storage/'.$filename);
        return url('/uploads/'.$filename);
    }

    public function searchMusic($keyword, $playlistID){
        $keyword = strtolower($keyword);
        if($playlistID == 0){
            return Music::latest()->where('status', '=', '1')->whereRaw("LOWER(judul) LIKE '%{$keyword}%'")->get();
        }else{
            return self::flatten(Playlist::where('id', $playlistID)->with('music')->with('allChildrenContent')->get(), '', $keyword);
        }
    }

    /////////////////////////

    protected function flatten($object, $path, $keyword = ''){
        $result = [];
        foreach ($object as $array){
            $curr_path = $path.'/'.$array['nama_playlist'];
            foreach($array['music'] as $item){
                if($keyword === '' || strpos(strtolower($item['judul']), strtolower($keyword)) !== false){
                    $item['folder_path'] = $curr_path;
                    array_push($result, $item);
                }
            }
            $result = array_merge($result, self::flatten($array['allChildrenContent'], $curr_path, $keyword));         
        }
        is_object($result) ? $result = $result->toArray() : $result;
        return array_filter($result);
    }

    protected static function convert_latin1_to_utf8($dat){
        if(is_string($dat)){
            return utf8_encode($dat);
        }else if(is_array($dat)){
            $ret = [];
            foreach ($dat as $i => $d) $ret[ $i ] = self::convert_latin1_to_utf8($d);
    
            return $ret;
        }else if(is_object($dat)){
            foreach ($dat as $i => $d) $dat->$i = self::convert_latin1_to_utf8($d);
    
            return $dat;
        }else{
            return $dat;
        }
    }
}