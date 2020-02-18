<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Playlist;
use App\MusicPlaylist;
use App\Config;

class PlaylistController extends Controller
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
        $playlist = new Playlist;
        try{
            $allPlaylist = $playlist->tree();
        }catch(Exception $e){
            //no parent category found
        } 
        return $allPlaylist;
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
        if($request->act){
            if($request->act == 'add_to_playlist'){
                $music_id = $request->music_id;

                foreach($request->playlist_arr as $playlist_id){
                    $playlist_detail = new MusicPlaylist;
                    $playlist_detail->music_id = $music_id;
                    $playlist_detail->playlist_id = $playlist_id;
                    $playlist_detail->save();
                }
            }if($request->act == 'rename_playlist'){
                return Playlist::where('id', $request->playlist_id)->update(['nama_playlist' => $request->playlist_new_name]);
            }
            return response()->json(array('act' => $request->act, 'success' => true), 200);
        }else{
            $this->validate($request, [
                'nama_playlist' => 'required|max:191',
                'parent_id' =>'required',
            ]);
            
            $playlist = new Playlist;
            $playlist->nama_playlist = $request->nama_playlist;
            $playlist->parent_id = $request->parent_id;
            $playlist->created_by = auth('api')->user()->id;
            $playlist->save();

            return response()->json(array('success' => $playlist->id), 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    { //return $id;
        if($id == 'playlistSelection'){
            $playlist = new Playlist;
            $allPlaylist = $playlist->tree();

            $result = self::playlistSelection($allPlaylist);
        }else if($id == 'getOuterPlaylistComposition'){
            $outerPlaylist = Playlist::where(['status' => 1, 'parent_id' => 0])->orderBy('nama_playlist', 'asc')->get();

            $result = [];
            foreach($outerPlaylist as $item){
                $temp_array = array(
                    'label' => $item['nama_playlist'],
                    'number' => collect(self::flatten(Playlist::where(['status' => 1, 'id' => $item['id']])
                                ->with('music')->with('allChildrenContent')->get(), ''))->count(),
                );
                
                array_push($result, $temp_array);
            }
        }else if($id == 'getOuterPlaylistFilesize'){
            $outerPlaylist = Playlist::where(['status' => 1, 'parent_id' => 0])->orderBy('nama_playlist', 'asc')->get();

            $result = [];
            foreach($outerPlaylist as $item){
                $temp_array = array(
                    'label' => $item['nama_playlist'],
                    'number' => round(collect(self::flatten(Playlist::where(['status' => 1, 'id' => $item['id']])
                                ->with('music')->with('allChildrenContent')->get(), ''))->sum('filesize')/pow(1024, 3), 2),
                );
                
                array_push($result, $temp_array);
            }
        }
        return $result;
    }

    protected function playlistSelection($allPlaylist){
        $path = '';
        $playlistArr = [];
        
        $result = self::playlistBreakdown($path, $allPlaylist, $playlistArr);
        
        return $result;
    }

    protected function playlistBreakdown($path, $allPlaylist, $playlistArr){
        foreach($allPlaylist as $item){
            $node = $path.' / '.ucwords($item['nama_playlist']);
            $new_item = array(
                'id' => $item['id'],
                'path' => $node,
            );
            array_push($playlistArr, $new_item);

            if(count($item['children']) > 0){
                $playlistArr = self::playlistBreakdown($node, $item['children'], $playlistArr);
            }
        }

        return $playlistArr;
    }

    protected function flatten($object, $path){
        $result = [];
        foreach ($object as $array){
            $curr_path = $path.'/'.$array['nama_playlist'];
            foreach($array['music'] as $item){
                $item['folder_path'] = $curr_path;
                array_push($result, $item);
            }
            $result = array_merge($result, self::flatten($array['allChildrenContent'], $curr_path));         
        }
        is_object($result) ? $result = $result->toArray() : $result;
        return array_filter($result);
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
        self::delete_child_playlist($id);
    }

    protected function delete_child_playlist($id){
        $child_playlist = Playlist::where('parent_id', $id)->get();
        foreach($child_playlist as $child){
            self::delete_child_playlist($child['id']);
        }

        MusicPlaylist::where('playlist_id', $id)->delete();
        return Playlist::where('id', $id)->delete();
    }
}
