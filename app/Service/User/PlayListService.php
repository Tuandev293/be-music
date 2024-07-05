<?php

namespace App\Service\User;

use App\Models\LogListen;
use App\Models\PlayList;
use App\Models\PlayListDetail;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlayListService
{
    /**
     * createPlayLis
     *
     * @param   $request 
     *
     * @return  \Illuminate\Database\Eloquent\Collection|array
     */
    public function createPlayList($request)
    {
        try {
            DB::beginTransaction();
            $songId = $request->input('song_id');
            $title = $request->input('title');
            $userId = Auth::guard('api')->user()->id;
            $playList = PlayList::create([
                'title' => $title,
                'user_id' => $userId
            ]);
            PlayListDetail::create([
                'song_id' => $songId,
                'playlist_id' => $playList->id,
            ]);
            DB::commit();
            return $playList;
        } catch (Exception $e) {
            Log::error("[PlayListService][createPlayList] error because" . $e->getMessage());
            DB::rollBack();
            throw new Exception('[PlayListService][createPlayList] error because ' . $e->getMessage());
        }
    }
    /**
     * listPlayList
     *
     * @param   $request 
     *
     * @return  \Illuminate\Database\Eloquent\Collection|array
     */
    public function listPlayList()
    {
        try {
            $userId = Auth::guard('api')->user()->id;
            $playlists = User::where('users.id', $userId)
                            ->join('playlists', 'users.id', 'playlists.user_id')
                            ->leftJoin('play_list_details', 'playlists.id', 'play_list_details.playlist_id')
                            ->leftJoin('songs', 'play_list_details.song_id', 'songs.id')
                            ->select([
                                'playlists.id as playlist_id',
                                DB::raw('MAX(songs.original_image) as original_image'), // Lấy original_image của bài hát mới nhất
                                'playlists.title',
                                'playlists.created_at',
                                DB::raw('DATE_FORMAT(playlists.created_at, "%d-%m-%Y") as date_create')
                            ])
                            ->groupBy('playlists.id', 'playlists.title', 'playlists.created_at')
                            ->distinct()
                            ->get();
            return $playlists;
        } catch (Exception $e) {
            Log::error("[PlayListService][listPlayList] error because" . $e->getMessage());
            DB::rollBack();
            throw new Exception('[PlayListService][listPlayList] error because ' . $e->getMessage());
        }
    }

    /**
     * listSongInPlayList
     *
     * @param   $request 
     *
     * @return  \Illuminate\Database\Eloquent\Collection|array
     */
    public function listSongInPlayList($id)
    {
        try {
            $userId = Auth::guard('api')->user()->id;
            $playList = $this->checkPermissionPlayList($userId, $id);
            if (empty($playList)) {
                return ['error_status' => PlayList::STATUS_ERROR];
            };
            $subQuery = LogListen::query()->select([DB::raw('COUNT(log_listen.id) as total_listen'), 'song_id as listen_song_id'])->groupBy('log_listen.song_id');
            $result = PlayListDetail::where('playlist_id', $id)
                            ->join('songs', function ($qr) {
                                $qr-> on('play_list_details.song_id', 'songs.id')
                                    ->whereNull('songs.deleted_at');
                            })
                            ->leftJoinSub($subQuery, 'sub_query', 'songs.id', '=', 'sub_query.listen_song_id')
                            ->leftJoin('albums', 'songs.album_id', 'albums.id')
                            ->leftJoin('artists', 'songs.artist_id', 'artists.id')
                            ->leftJoin('song_category', 'songs.category_id', 'song_category.id')
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
                                'song_category.id as category_id',
                                'song_category.title as category_title',
                                DB::raw('IFNULL(sub_query.total_listen, 0) as total_listen'),
                                DB::raw('DATE_FORMAT(songs.created_at, "%d-%m-%Y") as date_create')
                            ])->get();
            return $result;
        } catch (Exception $e) {
            Log::error("[PlayListService][listSongInPlayList] error because" . $e->getMessage());
            DB::rollBack();
            throw new Exception('[PlayListService][listSongInPlayList] error because ' . $e->getMessage());
        }
    }
    /**
     * delete PlayList
     *
     * @param   $request 
     *
     * @return  bool|array
     */
    public function deletePlayList($playListId)
    {
        try {
            DB::beginTransaction();
            $userId = Auth::guard('api')->user()->id;
            $playList = $this->checkPermissionPlayList($userId, $playListId);
            if (empty($playList)) {
                return ['error_status' => PlayList::STATUS_ERROR];
            };
            $playList = PlayList::where('user_id', $userId)->where('id', $playListId)->delete();
            PlayListDetail::where('playlist_id', $playListId)->delete();
            DB::commit();
            return true;
        } catch (Exception $e) {
            Log::error("[PlayListService][deletePlayList] error because" . $e->getMessage());
            DB::rollBack();
            throw new Exception('[PlayListService][deletePlayList] error because ' . $e->getMessage());
        }
    }
    /**
     * addSongPlaylist
     *
     * @param   $request 
     *
     */
    public function addSongPlaylist($request)
    {
        try {
            $userID = Auth::guard('api')->user()->id;
            $playListId = $request->input('playlist_id');
            $songId = $request->input('song_id');
            $playList = $this->checkPermissionPlayList($userID, $playListId);
            if (empty($playList)) {
                return ['error_status' => PlayList::STATUS_ERROR];
            };
            $playListDetail = PlayListDetail::query()->firstOrCreate([
                'song_id' => $songId,
                'playlist_id' => $playListId,
            ], [
                'song_id' => $songId,
                'playlist_id' => $playListId,
            ]);
            return $playListDetail;
        } catch (Exception $e) {
            Log::error("[PlayListService][addSongPlaylist] error because" . $e->getMessage());
            DB::rollBack();
            throw new Exception('[PlayListService][addSongPlaylist] error because ' . $e->getMessage());
        }
    }

    /**
     * addSongPlaylist
     *
     * @param   $request 
     *
     */
    public function removeSongPlayList($request)
    {
        try {
            $userID = Auth::guard('api')->user()->id;
            $playListId = $request->input('playlist_id');
            $songId = $request->input('song_id');
            $playList = $this->checkPermissionPlayList($userID, $playListId);
            if (empty($playList)) {
                return ['error_status' => PlayList::STATUS_ERROR];
            };
            $playListDetail = PlayListDetail::query()->where('song_id', $songId)
                ->where('playlist_id', $playListId)
                ->delete();
            return $playListDetail;
        } catch (Exception $e) {
            Log::error("[PlayListService][removeSongPlayList] error because" . $e->getMessage());
            DB::rollBack();
            throw new Exception('[PlayListService][removeSongPlayList] error because ' . $e->getMessage());
        }
    }
    /**
     * checkPermissionPlayList
     * @param   $userId 
     * @param   $playListId 
     * @return  \Illuminate\Database\Eloquent\Collection
     */
    public function checkPermissionPlayList($userId, $playListId)
    {
        $playList = PlayList::where('user_id', $userId)->where('id', $playListId)->first();
        return $playList;
    }
}
