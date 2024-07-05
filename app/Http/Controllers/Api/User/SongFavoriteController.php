<?php

namespace App\Http\Controllers\Api\User;

use App\Core\AbstractApiController;
use App\Http\Requests\User\UserFavoriteRequest;
use App\Models\SongFavorite;
use App\Service\User\UserService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class SongFavoriteController extends AbstractApiController
{
    /**
     * @var UserService
     */
    private $userService;
    /**
     * @param UserService $userRepository
     */
    public function __construct(
        UserService $userService
    ) {
        $this->userService = $userService;
    }
    /**
     *  get favorite song
     * @return  \Illuminate\Http\JsonResponse
     */
    public function getFavoriteSong(Request $request)
    {
        try {
            $userId = Auth::guard('api')->user()->id;
            $data = $this->userService->getFavoriteSong($userId, $request);
            return $this->respondWithPagination($data, __('messages.songFavorite.list.success'));
        } catch (Exception $e) {
            Log::error("[SongFavorite][getFavoriteSong] error because" . $e->getMessage());
            throw new Exception('[SongFavorite][getFavoriteSong] error because ' . $e->getMessage());
        }
    }

    /**
     * favorite doctor
     * @param UserFavoriteRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function favoriteSong(UserFavoriteRequest $request)
    {
        try {
            DB::beginTransaction();
            $songId = $request->input('song_id');
            $userId = Auth::guard('api')->user()->id;
            $userFavoriteSong =SongFavorite::where('user_id', $userId)->where('song_id', $songId)->first();
            if (empty($userFavoriteSong)) {
               SongFavorite::create([
                    'song_id' => $songId,
                    'user_id' => $userId
                ]);
                DB::commit();
                return $this->respondUpdated(null, __('messages.songFavorite.action.like'));
            } else {
                $userFavoriteSong->delete();
                DB::commit();
                return $this->respondUpdated(null, __('messages.songFavorite.action.unLike'));
            };
        } catch (Exception $e) {
            Log::error("[UserController][favoriteDoctor] error because" . $e->getMessage());
            DB::rollBack();
            throw new Exception('[UserController][favoriteDoctor] error because ' . $e->getMessage());
        }
    }
}
