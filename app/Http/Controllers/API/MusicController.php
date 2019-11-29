<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Playlist;
use App\Music;
use App\PlaylistDetail;
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
        if($request->hasFile('file_name')){ return json_encode($request);
            foreach($request->file_name as $item){
                $originalFilename = $item->getClientOriginalName();
                $extension = $item->getClientOriginalExtension();
                $filenameOnly = pathinfo($originalFilename, PATHINFO_FILENAME);
                $filename = str_slug($filenameOnly).'-'.time().'.'.$extension;
                $item->storeAs('public/uploadedMusic', $filename);
                
                $music = new Music;
                $music->judul = $filenameOnly;
                $music->path = 'uploadedMusic/'.$filename;
                $music->uploaded_by = auth('api')->user()->id;
                $music->save();

                $music_id = $music->id;
                $playlist_arr = explode(',', $request->playlist);
                foreach($playlist_arr as $id){
                    $playlist_detail = new PlaylistDetail;
                    $playlist_detail->music_id = $music_id;
                    $playlist_detail->playlist_id = $id;
                    $playlist_detail->save();
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

                return PlaylistDetail::where('id', $request->playlist_detail_id)->update(['status' => -1]);
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
        //return $id;
        if($id == 'plain_playlist'){ //return $id;
            return Playlist::where('status', '1')->orderBy('nama_playlist', 'asc')->get();
        }else if($id == 'getMusicListByUploadDate'){
            return Music::latest()->where('status', '=', '1')->paginate(10);
        }else if($id == 'getMusicListByTitle'){
            return Music::orderBy('judul', 'ASC')->where('status', '=', '1')->paginate(10);
        }else if(strpos($id, 'playlist') !== false){
            //get playlist index
            $index = strpos($id, '-');
            $playlist = substr($id, ($index+1));

            //get sorting param
            preg_match('~_(.*?)@~', $id, $param); //get result in $param[1]
            preg_match('~@(.*?)-~', $id, $ascdesc); //return $ascdesc;

            //return $playlist;
            //return Music::where('di_playlist', 'LIKE', "%$playlist%")->latest()->where('status', '=', '1')->paginate(10);

            return DB::table('playlists')
                ->select('playlists.nama_playlist', 'playlist_details.id', 'musics.judul', 'musics.path', 'playlist_details.created_at')
                ->leftJoin('playlist_details', 'playlist_details.playlist_id', '=', 'playlists.id')
                ->leftJoin('musics', 'playlist_details.music_id', '=', 'musics.id')
                ->where([
                    ['playlists.id', '=', $playlist],
                    ['playlist_details.status', '=', 1],
                ])
                ->orderBy($param[1], $ascdesc[1])->orderBy('playlist_details.created_at', 'DESC')->paginate(10);
        }
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
        return Music::where('id', $id)->update(['status' => -1]);
    }
}
