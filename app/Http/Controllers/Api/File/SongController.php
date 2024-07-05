<?php

namespace App\Http\Controllers\Api\File;

use App\Core\AbstractApiController;
use App\Http\Requests\Admin\SongStoreRequest;
use App\Models\Song;
use App\Service\File\SongService;
use Exception;
use Illuminate\Http\Request;

class SongController extends AbstractApiController
{
    protected $songService;

    public function __construct(SongService $songService)
    {
        $this->songService = $songService;
    }

    /**
     * get list song
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function list(Request $request)
    {
        try {
            $songs = $this->songService->getListSong($request);
            return $this->respondWithPagination($songs, __('messages.song.list.success'));
        } catch (Exception $e) {
            throw new Exception('[UserController][store] error because ' . $e->getMessage());
        }
    }

    /**
     * get list category song
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function listCategory()
    {
        try {
            $category = $this->songService->getListCategorySong();
            return $this->renderJsonResponse($category, __('messages.song.list-category.success'));
        } catch (Exception $e) {
            throw new Exception('[UserController][store] error because ' . $e->getMessage());
        }
    }
    /**
     * get list song popular
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function listPopular(Request $request)
    {
        try {
            $songs = $this->songService->getListSongPopular($request);
            return $this->respondWithPagination($songs, __('messages.song.list-popular.success'));
        } catch (Exception $e) {
            throw new Exception('[UserController][store] error because ' . $e->getMessage());
        }
    }
    /**
     * store
     * @param SongStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */

    public function store(SongStoreRequest $request)
    {
        try {
            $song = $this->songService->createSong($request);
            return $this->respondCreated($song, __('messages.song.create.success'));
        } catch (Exception $e) {
            throw new Exception('[UserController][store] error because ' . $e->getMessage());
        }
    }
    /**
     * store
     * @param SongStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */

    public function update(SongStoreRequest $request)
    {
        try {
            $song = $this->songService->updateSong($request);
            return $this->renderJsonResponse($song, __('messages.song.update.success'));
        } catch (Exception $e) {
            throw new Exception('[UserController][store] error because ' . $e->getMessage());
        }
    }
    /**
     * store
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */

    public function delete(Request $request)
    {
        try {
            $songId = $request->input('song_id');
            if(empty($songId)){
                return $this->respondBadRequest(__('messages.song.delete.fail'));
            }
            Song::where("id", $songId)->delete();
            return $this->renderJsonResponse([], __('messages.song.delete.success'));
        } catch (Exception $e) {
            throw new Exception('[UserController][store] error because ' . $e->getMessage());
        }
    }
}
