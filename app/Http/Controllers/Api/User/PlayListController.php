<?php

namespace App\Http\Controllers\Api\User;

use App\Core\AbstractApiController;
use App\Http\Requests\User\AddSongPlayListRequest;
use App\Http\Requests\User\PlayListCreateRequest;
use App\Http\Requests\User\RemoveSongPlayListRequest;
use App\Service\User\PlayListService;
use Exception;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlayListController extends AbstractApiController
{

    protected $playListService;
    public function __construct(PlayListService $playListService)
    {
        $this->playListService = $playListService;
    }

    /**
     * createPlayLis
     *
     * @param   $request 
     *
     * @return  \Illuminate\Http\JsonResponse
     */
    public function createPlayList(PlayListCreateRequest $request)
    {
        try {
            $data = $this->playListService->createPlayList($request);
            return $this->renderJsonResponse($data, __('messages.playlist.create.success'));
        } catch (Exception $e) {
            Log::error("[PlayListController][createPlayList] error because" . $e->getMessage());
            DB::rollBack();
            throw new Exception('[PlayListController][createPlayList] error because ' . $e->getMessage());
        }
    }
    /**
     * createPlayLis
     *
     * @param   $request 
     *
     * @return  \Illuminate\Http\JsonResponse
     */
    public function listPlayList()
    {
        try {
            $data = $this->playListService->listPlayList();
            return $this->renderJsonResponse($data, __('messages.playlist.list.success'));
        } catch (Exception $e) {
            Log::error("[PlayListController][listPlayList] error because" . $e->getMessage());
            DB::rollBack();
            throw new Exception('[PlayListController][listPlayList] error because ' . $e->getMessage());
        }
    }

    /**
     * listSongInPlayList
     *
     * @param   $request 
     *
     * @return  \Illuminate\Http\JsonResponse
     */
    public function listSongInPlayList($playListId)
    {
        try {
            $data = $this->playListService->listSongInPlayList($playListId);
            if(!empty($data['error_status'])) {
                return $this->respondBadRequest(__('messages.playlist.list_song.fail'));
            }   
            return $this->renderJsonResponse($data, __('messages.playlist.list_song.success'));
        } catch (Exception $e) {
            Log::error("[PlayListController][listSongInPlayList] error because" . $e->getMessage());
            DB::rollBack();
            throw new Exception('[PlayListController][listSongInPlayList] error because ' . $e->getMessage());
        }
    }
    /**
     * deletePlayList
     *
     * @param   $playlistId 
     *
     * @return  \Illuminate\Http\JsonResponse
     */
    public function deletePlayList($playlistId)
    {
        try {
            $data = $this->playListService->deletePlayList($playlistId);
            if(!empty($data['error_status'])) {
                return $this->respondBadRequest(__('messages.playlist.remove.fail'));
            }   
            return $this->renderJsonResponse($data, __('messages.playlist.remove.success'));
        } catch (Exception $e) {
            Log::error("[PlayListController][deletePlayList] error because" . $e->getMessage());
            DB::rollBack();
            throw new Exception('[PlayListController][deletePlayList] error because ' . $e->getMessage());
        }
    }
    /**
     * add song playlist
     *
     * @param   $request 
     *
     * @return  \Illuminate\Http\JsonResponse
     */
    public function addSongPlayList(AddSongPlayListRequest $request)
    {
        try {
            $data = $this->playListService->addSongPlaylist($request);
            if(!empty($data['error_status'])) {
                return $this->respondBadRequest(__('messages.playlist.add_song.fail'));
            }   
            return $this->renderJsonResponse($data, __('messages.playlist.add_song.success'));
        } catch (Exception $e) {
            Log::error("[PlayListController][addSongPlayList] error because" . $e->getMessage());
            DB::rollBack();
            throw new Exception('[PlayListController][addSongPlayList] error because ' . $e->getMessage());
        }
    }

        /**
     * add song playlist
     *
     * @param   $request 
     *
     * @return  \Illuminate\Http\JsonResponse
     */
    public function removeSongPlayList(RemoveSongPlayListRequest $request)
    {
        try {
            $data = $this->playListService->removeSongPlayList($request);
            if(!empty($data['error_status'])) {
                return $this->respondBadRequest(__('messages.playlist.remove_song.fail'));
            }   
            return $this->renderJsonResponse(null, __('messages.playlist.remove_song.success'));
        } catch (Exception $e) {
            Log::error("[PlayListController][removeSongPlayList] error because" . $e->getMessage());
            DB::rollBack();
            throw new Exception('[PlayListController][removeSongPlayList] error because ' . $e->getMessage());
        }
    }
}
