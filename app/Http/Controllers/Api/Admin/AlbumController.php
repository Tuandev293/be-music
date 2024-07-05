<?php

namespace App\Http\Controllers\Api\Admin;

use App\Core\AbstractApiController;
use App\Http\Requests\Admin\AlbumStoreRequest;
use App\Http\Requests\Admin\ArtistStoreRequest;
use App\Service\Admin\AlbumService;
use App\Service\Admin\ArtistService;
use Exception;
use Illuminate\Http\Request;

class AlbumController extends AbstractApiController
{
    protected $albumService;

    public function __construct(AlbumService $albumService)
    {
        $this->albumService = $albumService;
    }

    /**
     * get list album
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function list(Request $request)
    {
        try {
            $albums = $this->albumService->getListAlbum($request);
            return $this->respondWithPagination($albums, __('messages.album.list.success'));
        } catch (Exception $e) {
            throw new Exception('[UserController][store] error because ' . $e->getMessage());
        }
    }
    /**
     * get list album
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function listSong(Request $request)
    {
        try {
            if(empty($request->get('album_id'))){
                return $this->respondBadRequest(__('messages.song.list.fail'));
            }
            $albums = $this->albumService->getListSongAlbum($request);
            return $this->respondWithPagination($albums, __('messages.song.list.success'));
        } catch (Exception $e) {
            throw new Exception('[UserController][store] error because ' . $e->getMessage());
        }
    }
    /**
     * store album
     * @param AlbumStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */

    public function store(AlbumStoreRequest $request)
    {
        try {
            $album = $this->albumService->createAlbum($request);
            return $this->respondCreated($album, __('messages.album.create.success'));
        } catch (Exception $e) {
            throw new Exception('[UserController][store] error because ' . $e->getMessage());
        }
    }

    /**
     * delete album
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */

     public function delete(Request $request)
     {
        try {
            $artist = $this->albumService->deleteAlbum($request);
            if(empty($artist)){
                return $this->respondBadRequest(__('messages.album.delete.fail'));
            }
            return $this->renderJsonResponse([], __('messages.album.delete.success'));
        } catch (Exception $e) {
            throw new Exception('[UserController][store] error because ' . $e->getMessage());
        }
    }

    /**
     * update album
     * @param AlbumStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function update(AlbumStoreRequest $request)
    {
        try {
            $artist = $this->albumService->updateAlbum($request);
            return $this->renderJsonResponse($artist, __('messages.album.update.success'));
        } catch (Exception $e) {
            throw new Exception('[UserController][store] error because ' . $e->getMessage());
        }
    }
}
