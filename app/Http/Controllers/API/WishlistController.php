<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Wishlist;
use App\WishlistDetail;

class WishlistController extends Controller
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
            ->select('wishlists.id', 'wishlists.created_at', 'musics.judul', 'musics.path')
            ->rightJoin('wishlists', 'wishlists.music_id', '=', 'musics.id')
            ->leftJoin('users', 'wishlists.user_id', '=', 'users.id')
            ->where('user_id', '=', auth('api')->user()->id)
            ->paginate(10);
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
            if($request->act == 'create_wishlist'){
                $wishlist = new Wishlist;
                $wishlist->wishlist_label = $request->wishlist_label;
                $wishlist->user_id = auth('api')->user()->id;
                $wishlist->save();

                return response()->json(array('success' => true, 'last_insert_id' => $wishlist->id), 200);
            }else if($request->act == 'add_to_wishlist'){
                foreach($request->selected_wishlist as $wishlist){
                    $item = new WishlistDetail;
                    $item->wishlist_id = $wishlist;
                    $item->music_id = $request->music_id;
                    $item->save();
                }

                return response()->json(array('success' => true, 'last_insert_id' => $item->id), 200);
            }else if($request->act == 'delete_from_wishlist'){
                return WishlistDetail::where('id', $request->id)->delete();
            }else if($request->act == 'modify_wishlist_stat'){
                return Wishlist::where('id', $request->id)->update(['status' => $request->status]);
            }
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
        if($id == 'wishlistSelection'){
            return Wishlist::where('status', '=', '0')
                ->where('user_id', '=', auth('api')->user()->id)
                ->orderBy('created_at', 'DESC')
                ->withCount('detail')
                ->get();
        }else if($id == 'get_wishlist'){
            return Wishlist::where('user_id', '=', auth('api')->user()->id)
                ->orderByRaw("FIELD(status , '0', '1', '-1') ASC")
                ->orderBy('created_at', 'DESC')
                ->withCount('detail')
                ->paginate(10);
        }else if(strpos($id, 'get_wishlist_detail') !== false){
            $index = strpos($id, '-');
            $wishlist = substr($id, ($index+1));
            return WishlistDetail::where('wishlist_id', '=', $wishlist)
                ->orderBy('created_at')
                ->with('music')
                ->paginate(10);
        }
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
