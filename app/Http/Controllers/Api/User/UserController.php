<?php

namespace App\Http\Controllers\Api\User;

use App\Core\AbstractApiController;
use App\Http\Requests\User\UserLoginRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdatePasswordRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Requests\User\VerifyRegisterRequest;
use App\Models\User;
use App\Service\User\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserController extends AbstractApiController
{
    /**
     * @var UserService
     */
    protected $userService;

    public function __construct(
        UserService $userService,
    )
    {
        $this->userService = $userService;
    }
    /**
     * Login
     * @param UserStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function store(UserStoreRequest $request)
    {
        try {
            $user = $this->userService->storeUser($request);
            return $this->respondCreated($user, __('messages.user.register.success'));
        } catch (Exception $e) {
            throw new Exception('[UserController][store] error because ' . $e->getMessage());
        }
    }

    /**
     * Verify Register
     * @param VerifyRegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */

     public function verifyCodeRegister(VerifyRegisterRequest $request)
     {
        try {
            $user = $this->userService->verifyRegister($request);
            if (empty($user)) {
                return $this->respondWithError(__('messages.user.verifyRegister.fail'));
            }
            return $this->renderJsonResponse($user, __('messages.user.verifyRegister.success'));
        } catch (Exception $e) {
            throw new Exception('[UserController][verifyRegister] error because ' . $e->getMessage());
        }
     }

    /**
     * @param UserLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function login(UserLoginRequest $request)
    {
        try {
            $user = $this->userService->loginByAccount($request);
            if (empty($user)) {
                return $this->respondBadRequest(__('messages.user.login.fail'));
            } elseif (isset($user['code'])) {
                return $this->renderJsonResponse($user, __('messages.user.login.warring'));
            }
            return $this->renderJsonResponse($user, __('messages.user.login.success'));
        } catch (Exception $e) {
            Log::error("[UserController][login] error because" . $e->getMessage());
            throw new Exception('[UserController][login] error because ' . $e->getMessage());
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function getUserLogin()
    {
        try {
            $user = Auth::guard('api')->user();
            $data = [
                'id'=> $user->id,
                'name'=> $user->name,
                'avatar'=> $user->avatar,
                'email'=> $user->email,
                'cover_image'=> $user->cover_image,
                'phone'=> $user->phone,
                'gender'=> $user->gender,
                'birthday'=> $user->birthday,
                'is_vip' => !empty($user->date_end_vip) && !empty($user->date_start_vip) ? User::VIP : User::NOT_VIP,
                "date_end_vip" => $user->date_end_vip,
                "date_start_vip" => $user->date_start_vip
            ];
            if (empty($user)) {
                return $this->respondBadRequest(__('messages.user.login.fail'));
            } elseif (isset($user['code'])) {
                return $this->renderJsonResponse($data, __('messages.user.login.warring'));
            }
            return $this->renderJsonResponse($data, __('messages.user.login.success'));
        } catch (Exception $e) {
            Log::error("[UserController][login] error because" . $e->getMessage());
            throw new Exception('[UserController][login] error because ' . $e->getMessage());
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function updateProfile(UserUpdateRequest $request)
    {
        try {
            $user = $this->userService->updateProfile($request);
            return $this->renderJsonResponse($user, __('messages.user.update.success'));
        } catch (Exception $e) {
            Log::error("[UserController][login] error because" . $e->getMessage());
            throw new Exception('[UserController][login] error because ' . $e->getMessage());
        }
    }

    /**
     * @param UserUpdatePasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function updatePassword(UserUpdatePasswordRequest $request)
    {
        try {
            $user = $this->userService->updatePassword($request);
            if(empty($user)){
                return $this->respondBadRequest(__('messages.user.chanePassword.invalid_old_pass'));
            }
            return $this->renderJsonResponse($user, __('messages.user.chanePassword.success'));
        } catch (Exception $e) {
            Log::error("[UserController][login] error because" . $e->getMessage());
            throw new Exception('[UserController][login] error because ' . $e->getMessage());
        }
    }

    /**
     * download file
     *
     * @param   int  $id  
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse.
     */
    public function downloadSong(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            $filePath = $request->input('song_path') ?? '';
            if(empty($filePath) || !Storage::disk()->exists($filePath) || empty($user->date_start_vip)){
                return $this->respondNotFound('Not Found');
            }
            $publicPath = storage_path('app/public/' . $filePath);
            return response()->download($publicPath);
        } catch (\Throwable $e) {
            Log::error("[UserController][downloadSong] error because" . $e->getMessage());
            return $this->respondNotFound('Not Found');
        }
    }
}
