<?php

namespace App\Service\Admin;

use App\Common\Common;
use App\Helpers\UploadsHelper;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Song;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArtistService
{

    /**
     * create artist
     * @param Request $request
     * @return array
     * @throws \Exception
     */

    public function createArtist($request)
    {
        $pathAvatar = UploadsHelper::handleUploadFile(config('constants.fileImgArtistPath'), 'avatar', $request);
        $param = $request->only('name', 'gender');
        $song = Artist::create([
            "name" => $param['name'],
            "avatar" => $pathAvatar,
            "gender" => $param['gender'],
        ]);
        return $song;
    }
    /**
     * update artist
     * @param Request $request
     * @return array
     * @throws \Exception
     */

    public function updateArtist($request)
    {
        $param = $request->only('name', 'gender');
        if(!empty($request->file('avatar'))) {
            $param['avatar'] = UploadsHelper::handleUploadFile(config('constants.fileImgArtistPath'), 'avatar', $request);
        };
        Artist::where('id', $request->input('artist_id'))->update($param);
        $artist = Artist::where('id', $request->input('artist_id'))->select([
            'id',
            'artists.name',
            'artists.avatar',
            'artists.gender',
        ])->first();
        return $artist;
    }
    /**
     * get list song
     * @param Request $request
     * @return array
     * @throws \Exception
     */

    public function getListArtist($request)
    {
        $keyword = $request->get('keyword');
        $keywordChar = $request->get('keyword_char');
        $perPage = is_numeric($request->get('per_page')) ? (int) $request->get('per_page') : config('constants.perPage');
        $artist = Artist::when($keyword, function ($query) use ($keyword) {
            $search = Common::escapeLike($keyword);
            $query->where('artists.name', 'like', '%' . $search . '%');
        })->when($keywordChar, function ($query) use ($keywordChar) {
            $query->where('artists.name', 'like', $keywordChar . '%');
        })->select([
            'id',
            'artists.name',
            'artists.avatar',
            'artists.gender',
        ])->orderByDesc('artists.created_at')->paginate($perPage);
        return $artist;
    }
    /**
     * get list song
     * @param Request $request
     * @return array|null
     * @throws \Exception
     */

     public function getArtistDetail($request)
     {
        $artistsId = $request->get('artist_id');
        if(empty($artistsId)) return null;
        $detail = Artist::with(['songs', 'albums'])->select(['id', 'name', 'avatar', 'gender'])->where('artists.id', $artistsId)->get();
        return $detail;
     }
    /**
     * get list song
     * @param Request $request
     * @return array | null | boolean
     * @throws \Exception
     */

    public function deleteArtist($request)
    {
        try {
            $typeRemove = $request->get('type_remove');
            $artistId = $request->get('artist_id');
            $artist = Artist::where('id', $artistId)->first();
            if (empty($artist) || $artistId == Artist::ARTIST_OUTSIDE_SYSTEM) {
                return false;
            };
            DB::beginTransaction();
            $artist->delete();
            if ($typeRemove == Album::DELETE_SONG) {
                Song::where('artist_id', $artistId)->delete();
                Album::where('artist_id', $artistId)->delete();
            } else {
                Album::where('artist_id', $artistId)->update([
                    'artist_id' => Artist::ARTIST_OUTSIDE_SYSTEM
                ]);
                Song::where('artist_id', $artistId)->update([
                    'artist_id' => Artist::ARTIST_OUTSIDE_SYSTEM
                ]);
            }
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('[ArtistService][deleteArtist] error because ' . $e->getMessage());
            throw new Exception('[ArtistService][deleteArtist] error because ' . $e->getMessage());
        }
    }
}
