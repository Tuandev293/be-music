<?php
namespace App\Service\Admin;

use App\Common\Common;
use App\Helpers\UploadsHelper;
use App\Http\Requests\Admin\AlbumStoreRequest;
use App\Models\Album;
use App\Models\LogListen;
use App\Models\Song;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlbumService
{

    /**
    * check extension upload file
    * @param Request $request
    * @return array
    * @throws \Exception
    */

    public function createAlbum($request)
    {
        $pathAvatar = UploadsHelper::handleUploadFile(config('constants.fileImgAlbumPath'), 'cover_image', $request);
        $param = $request->only('title', 'release_date', 'artist_id');
        $releaseDate = Carbon::createFromFormat('d-m-Y', $param['release_date'])->format('Y-m-d');
        $album = Album::create([
            "title" => $param['title'],
            "artist_id" => $param['artist_id'],
            "cover_image" => $pathAvatar,
            "release_date" => $releaseDate
        ]);
        $album->release_date = Carbon::createFromFormat('Y-m-d', $album->release_date)->format('d-m-Y');;
        return $album;
    }

    /**
    * get list album
    * @param Request $request
    * @return array
    * @throws \Exception
    */

    public function getListAlbum($request)
    {
        $keyword = $request->get('keyword');
        $artistId = $request->get('artist_id');
        $perPage = is_numeric($request->get('per_page')) ? (int) $request->get('per_page') : config('constants.perPage');
        $albums = Album::leftJoin('artists', 'albums.artist_id', 'artists.id')->when($keyword, function ($query) use ($keyword) {
                                $search = Common::escapeLike($keyword);
                                $query->where('artists.name', 'like', '%' .$search. '%')
                                        ->orWhere('albums.title', 'like', '%'.$search.'%');
                        })->when($artistId, function ($qr) use ($artistId){
                            $qr->where('artist_id', $artistId);
                        })->select([
                            'albums.id',
                            'albums.title', 
                            'albums.release_date',
                            DB::raw('DATE_FORMAT(albums.release_date, "%d-%m-%Y") as release_date'), 
                            'artist_id',
                            'albums.cover_image as album_image',
                            'artists.name as artists_name',
                        ])->orderByDesc('albums.created_at')->paginate($perPage);
        return $albums;
    }

    /**
    * get list song in album
    * @param Request $request
    * @return array
    * @throws \Exception
    */

    public function getListSongAlbum($request)
    {
        try {
            $albumId = $request->get('album_id');
            $keyword = $request->get('key_word');
            $currentUser = Auth::guard('api')->user();
            $perPage = is_numeric($request->get('per_page')) ? (int) $request->get('per_page') : config('constants.perPage');
            $subQuery = LogListen::query()->select([DB::raw('COUNT(log_listen.id) as total_listen'), 'song_id as listen_song_id'])->groupBy('log_listen.song_id');
            $songs = Song::join('albums', 'songs.album_id', 'albums.id')->when($keyword, function ($query) use ($keyword) {
                                    $search = Common::escapeLike($keyword);
                                    $query->where('songs.name', 'like', '%' .$search. '%')
                                            ->orWhere('albums.title', 'like', '%'.$search.'%');
                            })
                            ->leftJoin('song_category', 'songs.category_id', 'song_category.id')
                            ->leftJoinSub($subQuery, 'sub_query','songs.id', '=', 'sub_query.listen_song_id')
                            ->leftJoin('artists', 'songs.artist_id', 'artists.id')
                            ->select([
                                'songs.title as song_name',
                                'songs.id as song_id',
                                'songs.duration as song_duration',
                                'songs.slug as song_slug',
                                'songs.file_path as song_path',
                                'albums.title as albums_name',
                                'artists.name as artists_name',
                                'songs.original_image',
                                'songs.thumbnail_image',
                                'albums.id as albums_id',
                                'artists.id as artists_id',
                                'song_category.id as category_id',
                                'song_category.title as category_title',
                                DB::raw('IFNULL(sub_query.total_listen, 0) as total_listen'),
                                DB::raw('DATE_FORMAT(songs.created_at, "%d-%m-%Y") as date_create'),
                            ])->orderByDesc('songs.created_at')->where('albums.id', $albumId);
            if (!empty($currentUser)) {
                $songs = $songs->leftJoin('song_favorites', function ($qr) use ($currentUser) {
                    $qr->on('songs.id', 'song_favorites.song_id')
                        ->where('song_favorites.user_id', $currentUser->id);
                })->addSelect([DB::raw('IF(song_favorites.song_id = songs.id AND song_favorites.user_id = ' . $currentUser->id . ', 1, 0) AS favorite')]);
            } else {
                $songs = $songs->addSelect([DB::raw("'0' as favorite"),]);
            }
            $songs = $songs->paginate($perPage);
            return $songs;
        } catch (Exception $e) {
            Log::error('[AlbumService][getListSongAlbum] error because ' . $e->getMessage());
            throw new Exception('[AlbumService][getListSongAlbum] error because ' . $e->getMessage());
        }
        
    }

    /**
    * delete album
    * @param Request $request
    * @return boolean
    * @throws \Exception
    */
    public function deleteAlbum($request)
    {
        try {
            $typeRemove = $request->get('type_remove');
            $albumId = $request->get('album_id');
            $album = Album::where('id', $albumId)->first();
            if(empty($album)){
                return false;
            };
            DB::beginTransaction();
            $album->delete();
            if($typeRemove == Album::DELETE_SONG) {
                Song::where('album_id', $albumId)->delete();
            }else{
                Song::where('album_id', $albumId)->update([
                    'album_id' => null
                ]);
            }
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('[AlbumService][deleteAlbum] error because ' . $e->getMessage());
            throw new Exception('[AlbumService][deleteAlbum] error because ' . $e->getMessage());
        }
    }

    /**
     * update album
     * @param AlbumStoreRequest $request
     * @return array
     * @throws \Exception
     */

    public function updateAlbum($request)
    {
        $param = $request->only('title', 'release_date', 'artist_id');
        if (!empty($request->file('cover_image'))) {
            $param['cover_image'] = UploadsHelper::handleUploadFile(config('constants.fileImgAlbumPath'), 'cover_image', $request);
        };
        $param['release_date'] = Carbon::createFromFormat('d-m-Y', $param['release_date'])->format('Y-m-d');
        Album::where('id', $request->input('album_id'))->update($param);
        $albums = Album::leftJoin('artists', 'albums.artist_id', 'artists.id')->select([
                'albums.id',
                'albums.title', 
                'albums.release_date',
                'artists.name as artist_name',
                DB::raw('DATE_FORMAT(albums.release_date, "%d-%m-%Y") as release_date'), 
                'artist_id'
            ])->where('albums.id', $request->input('album_id'))->first();
        return $albums;
    }
}
