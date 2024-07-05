<?php

namespace App\Http\Controllers\Api\Admin;

use App\Core\AbstractApiController;
use App\Http\Requests\Admin\ArtistStoreRequest;
use App\Service\Admin\ArtistService;
use Exception;
use Illuminate\Http\Request;

class ArtistController extends AbstractApiController
{
    protected $artistService;

    public function __construct(ArtistService $artistService)
    {
        $this->artistService = $artistService;
    }

    /**
     * get list artist
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function list(Request $request)
    {
        try {
            $artists = $this->artistService->getListArtist($request);
            return $this->respondWithPagination($artists, __('messages.artist.list.success'));
        } catch (Exception $e) {
            throw new Exception('[UserController][store] error because ' . $e->getMessage());
        }
    }

    /**
     * get list artist
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function artistDetail(Request $request)
    {
        try {
            $artists = $this->artistService->getArtistDetail($request);
            if(empty($artists)){
                return $this->respondBadRequest(__('messages.artist.detail.fail'));
            }
            return $this->renderJsonResponse($artists, __('messages.artist.detail.success'));
        } catch (Exception $e) {
            throw new Exception('[UserController][store] error because ' . $e->getMessage());
        }
    }
    /**
     * store artist
     * @param ArtistStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function store(ArtistStoreRequest $request)
    {
        try {
            $artist = $this->artistService->createArtist($request);
            return $this->respondCreated($artist, __('messages.artist.create.success'));
        } catch (Exception $e) {
            throw new Exception('[UserController][store] error because ' . $e->getMessage());
        }
    }

    /**
     * update artist
     * @param ArtistStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function update(ArtistStoreRequest $request)
    {
        try {
            $artist = $this->artistService->updateArtist($request);
            return $this->renderJsonResponse($artist, __('messages.artist.update.success'));
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
            $artist = $this->artistService->deleteArtist($request);
            if(empty($artist)){
                return $this->respondBadRequest(__('messages.artist.delete.fail'));
            }
            return $this->renderJsonResponse([], __('messages.artist.delete.success'));
        } catch (Exception $e) {
            throw new Exception('[UserController][store] error because ' . $e->getMessage());
        }
    }
}
