<?php

namespace App\Service\File;

use App\Common\Common;
use App\Helpers\UploadsHelper;
use App\Models\Artist;
use App\Models\LogListen;
use App\Models\Song;
use App\Models\SongCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SongService
{

    /**
     * create song 
     * @param Request $request
     * @return array
     * @throws \Exception
     */

    public function createSong($request)
    {
        $path = UploadsHelper::handleUploadFile(config('constants.fileSongPath'), 'song', $request);
        $pathOriginal = UploadsHelper::handleUploadFile(config('constants.fileImgSongPath'), 'original_image', $request);
        $pathThumbnail = UploadsHelper::handleUploadFile(config('constants.fileImgSongPath'), 'thumbnail_image', $request);
        $duration = UploadsHelper::getDurationSong($path);
        $param = $request->only('title', 'artist_id', 'album_id', 'category_id');
        $album = empty($param['album_id']) ? null : $param['album_id'];
        $song = Song::create([
            "title" => $param['title'],
            "artist_id" => $param['artist_id'],
            "category_id" => $param['category_id'],
            "album_id" => $param['artist_id'] != Artist::ARTIST_OUTSIDE_SYSTEM ? $album : null,
            "duration" => $duration,
            "file_path" => $path,
            "original_image" => $pathOriginal,
            "thumbnail_image" => $pathThumbnail,
            "slug" => $param['title'],
        ]);
        $songFormat = Song::leftJoin('albums', 'songs.album_id', 'albums.id')
            ->leftJoin('artists', 'songs.artist_id', 'artists.id')
            ->leftJoin('song_category', 'songs.category_id', 'song_category.id')
            ->select([
                'songs.title as song_name',
                'songs.id as song_id',
                'songs.duration as song_duration',
                'songs.slug as song_slug',
                'songs.file_path as song_path',
                'albums.title as albums_name',
                'albums.id as albums_id',
                'artists.id as artists_id',
                'artists.name as artists_name',
                'songs.original_image',
                'song_category.id as category_id',
                'song_category.title as category_title',
                'songs.thumbnail_image',
                DB::raw('DATE_FORMAT(songs.created_at, "%d-%m-%Y") as date_create')
            ])->where('songs.id', $song->id)->first();
        Log::info('line 63');
        return $songFormat;
    }

    /**
     * update song
     * @param Request $request
     * @return array
     * @throws \Exception
     */

    public function updateSong($request)
    {
        try {
            $param = $request->only('title', 'artist_id', 'album_id', 'category_id');
            $song = Song::where('id', $request->input('song_id'))->first();
            if (!empty($request->file('song'))) {
                $param['file_path'] = UploadsHelper::handleUploadFile(config('constants.fileSongPath'), 'song', $request);
                UploadsHelper::handleDeleteFile($song->path);
                $param['duration'] = UploadsHelper::getDurationSong($param['file_path']);
            };
            if (!empty($request->file('original_image'))) {
                $param['original_image'] = UploadsHelper::handleUploadFile(config('constants.fileImgSongPath'), 'original_image', $request);
                UploadsHelper::handleDeleteFile($song->original_image);
            }
            if (!empty($request->file('thumbnail_image'))) {
                $param['thumbnail_image'] = UploadsHelper::handleUploadFile(config('constants.fileImgSongPath'), 'thumbnail_image', $request);
                UploadsHelper::handleDeleteFile($song->thumbnail_image);
            }
            Song::where('id', $request->input('song_id'))->update($param);
            $songs = Song::leftJoin('albums', 'songs.album_id', 'albums.id')
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
                    'albums.id as albums_id',
                    'artists.id as artists_id',
                    'song_category.id as category_id',
                    'song_category.title as category_title',
                    DB::raw('DATE_FORMAT(songs.created_at, "%d-%m-%Y") as date_create')
                ])->where('songs.id', $request->input('song_id'))->first();
            return $songs;
        } catch (Exception $e) {
            Log::error("[SongService][updateSong] error because" . $e->getMessage());
            throw new Exception('[SongService][updateSong] error because ' . $e->getMessage());
        }
    }

    /**
     * update song
     * @param Request $request
     * @return array
     * @throws \Exception
     */

    public function getListCategorySong()
    {
        try {
            $songCategory = SongCategory::select(['id', 'title', 'slug'])->get();
            return $songCategory;
        } catch (Exception $e) {
            Log::error("[SongService][getListCategorySong] error because" . $e->getMessage());
            throw new Exception('[SongService][getListCategorySong] error because ' . $e->getMessage());
        }
    }
    /**
     * get list song
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws \Exception
     */

    public function getListSong($request)
    {
        $keyword = $request->get('keyword');
        $currentUser = Auth::guard('api')->user();
        $artistId =  $request->get('artists_id');
        $perPage = is_numeric($request->get('per_page')) ? (int) $request->get('per_page') : config('constants.perPage');
        $subQuery = LogListen::query()->select([DB::raw('COUNT(log_listen.id) as total_listen'), 'song_id as listen_song_id'])->groupBy('log_listen.song_id');
        $songs = Song::query()->leftJoin('albums', 'songs.album_id', 'albums.id')
            ->leftJoin('artists', 'songs.artist_id', 'artists.id')
            ->leftJoin('song_category', 'songs.category_id', 'song_category.id')
            ->leftJoinSub($subQuery, 'sub_query', 'songs.id', '=', 'sub_query.listen_song_id')
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($qr) use ($keyword) {
                    $search = Common::escapeLike($keyword);
                    $qr->where('songs.title', 'like', '%' . $search . '%')
                        ->orWhere('artists.name', 'like', '%' . $search . '%')
                        ->orWhere('albums.title', 'like', '%' . $search . '%');
                });
            })->select([
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
            ])->orderByDesc('songs.created_at');
        if (!empty($artistId)) {
            $songs->where('songs.artist_id', $artistId);
        }
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
    }

    /**
     * get list song popular
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws \Exception
     */

    public function getListSongPopular($request)
    {
        $keyword = $request->get('keyword');
        $currentUser = Auth::guard('api')->user();
        $perPage = is_numeric($request->get('per_page')) ? (int) $request->get('per_page') : config('constants.perPage');
        $subQuery = LogListen::query()->select([DB::raw('COUNT(log_listen.id) as total_listen'), 'song_id as listen_song_id'])
            ->groupBy('log_listen.song_id')
            ->orderByDesc('total_listen')
            ->limit(10);
        $songs = Song::query()->leftJoin('albums', 'songs.album_id', 'albums.id')
            ->leftJoin('artists', 'songs.artist_id', 'artists.id')
            ->joinSub($subQuery, 'sub_query', 'songs.id', '=', 'sub_query.listen_song_id')
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($qr) use ($keyword) {
                    $search = Common::escapeLike($keyword);
                    $qr->where('songs.title', 'like', '%' . $search . '%')
                        ->orWhere('artists.name', 'like', '%' . $search . '%')
                        ->orWhere('albums.title', 'like', '%' . $search . '%');
                });
            })->select([
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
                DB::raw('IFNULL(sub_query.total_listen, 0) as total_listen'),
                DB::raw('DATE_FORMAT(songs.created_at, "%d-%m-%Y") as date_create'),
            ])->orderByDesc('songs.created_at');
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
    }
}
