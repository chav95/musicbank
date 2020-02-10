<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;
use Response;
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
        // $getID3 = new \getID3;
        // return json_encode($getID3->analyze('http://172.18.11.32:8082/aud_uploads/wii shop theme jazz cover.mp3'));
        $playlist = $request->playlist;
        if($request->hasFile('file_name')){
            $s3Client = new S3Client([
                'region' => 'us-west-2',
                'version' => '2006-03-01',
            ]);
            
            /*$file = new \wapmorgan\Mp3Info\Mp3Info(Storage::disk('public')->path('uploadedMusic/hivi-remaja-official-lyric-video-1578297132.mp3'), true);
            return (array)$file;*/

            //$xml = new \SimpleXMLElement(Storage::disk('public')->path('Library.xml'), null, true); return $xml;
            //$xml->load(Storage::disk('public')->path('Library.xml')); //return (array)$xml;
            //return response()->json($xml->getElementsByTagName('key'));
            //return "<pre>".htmlspecialchars($xml, ENT_QUOTES, 'UTF-8')."</pre>";

            foreach($request->file_name as $item){
                $originalFilename = trim($item->getClientOriginalName());
                $extension = $item->getClientOriginalExtension();
                $filenameOnly = pathinfo($originalFilename, PATHINFO_FILENAME);
                //$filename = str_slug($filenameOnly).'-'.time().'.'.$extension;
                $filename = str_slug($filenameOnly).'.'.$extension;

                if(Storage::disk('ftp')->exists($originalFilename)){
                    return response()->json(array('error' => 'File already exist'), 500);
                }else{
                    // UPLOAD TO PROJECT FOLDER
                    //$item->storeAs('public/uploadedMusic', $filename);

                    // UPLOAD TO FTP SERVER
                    //return response()->json(array('upload' => Storage::disk('ftp')->put($originalFilename, fopen($item, 'r+'))));
                    //return response()->json(array('upload' => Storage::disk('ftp')->put($originalFilename, $item)));
                    Storage::disk('ftp')->put($originalFilename, fopen($item, 'r+'));
                    //return response()->json(array('upload' => Storage::disk('ftp')->exists($originalFilename)));

                    //$file = new \wapmorgan\Mp3Info\Mp3Info(Storage::disk('public')->path('uploadedMusic/'.$filename), true);
                    //$file = new \wapmorgan\Mp3Info\Mp3Info(Storage::disk('ftp')->get($originalFilename), true); return json_encode($file);
                    //$file = new \wapmorgan\Mp3Info\Mp3Info(url('http://172.18.11.32:8082/aud_uploads/'.str_replace(" ", "%20", str_replace("#", "%23", $originalFilename))), true); return $file->duration;
                    
                    $music = new Music;
                    $music->judul = $filenameOnly;
                    $music->filename = $originalFilename; //$filename;
                    $music->uploaded_by = auth('api')->user()->id;
                    $music->filetype = $extension;
                    $music->filesize = $item->getSize();

                    // if(isset($file->artist)){
                    //     $music->artis = $file->artist;
                    // }
                    // if(isset($file->album)){
                    //     $music->album = $file->album;
                    // }
                    // if(isset($file->genre)){
                    //     $music->genre = $file->genre;
                    // }
                    // if(isset($file->composer)){
                    //     $music->composer = $file->composer;
                    // }
                    // if(isset($file->tahun)){
                    //     $music->tahun = $file->tahun;
                    // }
                    // $music->filesize = $file->audioSize;
                    // $music->durasi = $file->duration;
                    // $music->bit_rate = $file->bitRate;
                    // $music->sample_rate = $file->sampleRate;

                    $music->save();
                    
                    $log = new Log;
                    $log->item_id = $music->id;
                    $log->action = 'upload music';
                    $log->item_name = $originalFilename;
                    $log->user_id = auth('api')->user()->id;
                    $log->save();

                    $music_id = $music->id;
                    $playlist_arr = explode(',', $request->playlist);
                    foreach($playlist_arr as $id){
                        $music_playlist = new MusicPlaylist;
                        $music_playlist->music_id = $music_id;
                        $music_playlist->playlist_id = $id;
                        $music_playlist->save();
                    }
                }
            }
            return response()->json(array('success' => true), 200);
        }else if($request->act){ //return $request;
            if($request->act == 'remove_from_playlist'){
                /*$get_playlist = DB::table('musics')
                    ->select('di_playlist')
                    ->where ('id', $request->music_id)
                    ->get();
                $di_playlist = str_replace(','.$request->playlist_id, '', $get_playlist[0]->di_playlist);
                //return $di_playlist;
                return DB::table('musics')
                    ->where('id', $request->music_id)
                    ->update(['di_playlist' => $di_playlist]);*/

                return MusicPlaylist::where('id', $request->music_playlist_id)->update(['status' => -1]);
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

            //return $playlist;
            //return Music::where('di_playlist', 'LIKE', "%$playlist%")->latest()->where('status', '=', '1')->paginate(10);
            
            /*return DB::table('playlists')
                ->select('playlists.nama_playlist', 'music_playlists.id', 'musics.judul', 'musics.path', 'music_playlists.created_at')
                ->leftJoin('music_playlists', 'music_playlists.playlist_id', '=', 'playlists.id')
                ->leftJoin('musics', 'music_playlists.music_id', '=', 'musics.id')
                ->where([
                    ['playlists.id', '=', $playlist],
                    ['music_playlists.status', '=', 1],
                ])
                ->orderBy($param[1], $ascdesc[1])->orderBy('music_playlists.created_at', 'DESC')->paginate(10);*/
            
            $result = self::flatten(Playlist::where('id', $playlist)->with('music')->with('allChildrenContent')->get(), '');
            $result = collect($result)->paginate(10); //$result['data'] = json_decode(json_encode($result->items())); //return $result;
            //is_object($result['data']) ? $result['data'] = $result['data']->toArray() : $result['data'];

            //$result = Music::latest()->where('status', '=', '1')->paginate(3);
            //return $result;//collect($result)->paginate(10);
            /*return Playlist::where('id', $playlist)
                ->with('music')
                ->with('allChildrenContent')
                ->paginate(10);*/
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

    protected function flatten($object, $path){
        $result = [];
        foreach ($object as $array){
            //return $array;
            $curr_path = $path.'/'.$array['nama_playlist'];
            foreach($array['music'] as $item){
                /*$result[] = array_filter($array, function($object){
                    return ! is_array($object);
                });*/
                $item['folder_path'] = $curr_path;
                //return $item;
                //result[] = $item;
                array_push($result, $item);
            }
            $result = array_merge($result, self::flatten($array['allChildrenContent'], $curr_path));         
        }
        is_object($result) ? $result = $result->toArray() : $result;
        return array_filter($result);
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
        MusicPlaylist::where('music_id', $id)->delete();
        return Music::where('id', $id)->update(['status' => -1]);
    }

    public function download($filename){ //return $filename;
        $getFile = Storage::disk('ftp')->get($filename);
        return Response::make($getFile, '200', array(
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"'
        ));
    }
}