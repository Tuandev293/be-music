<?php

namespace App\Http\Controllers\Api\Admin;

use App\Core\AbstractApiController;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Models\Admin;
use App\Models\GuardUtils;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminController extends AbstractApiController
{

    /**
     * @param AdminLoginRequest $request
     * @return \Illuminate\Http\JsonResponse | null
     * @throws Exception
     */
    public function login(AdminLoginRequest $request)
    {
        try {
            $email = $request->input('email');
            $guard = Admin::query()
                ->where('email', $email)
                ->select([
                    'admins.*',
                ])
                ->first();
            if (empty($guard) || !Hash::check($request->input('password'), $guard->password)) {
                return $this->respondBadRequest(__('messages.user.login.fail'));
            }
            $accessToken = $guard->createToken('AuthToken Admin', ['admin'])->accessToken;
            $data = [
                'id' => $guard->id,
                'name' => $guard->name,
                'gender' => $guard->gender,
                'type' => GuardUtils::ADMIN,
                'accessToken' => $accessToken
            ];
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
    public function getUserLogin()
    {
        try {
            $user = Auth::guard('admin')->user();
            $data = [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar,
                'gender' => $user->gender,
                'birthday' => $user->birthday
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
    public function getDashboard()
    {
        try {
            $result = DB::select(DB::raw('
                SELECT
                    "user" AS model,
                    COUNT(*) AS count,
                    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) AS create_today,
                    SUM(CASE WHEN WEEK(created_at) = WEEK(NOW()) THEN 1 ELSE 0 END) AS create_week,
                    SUM(CASE WHEN MONTH(created_at) = MONTH(NOW()) THEN 1 ELSE 0 END) AS create_month,
                    SUM(CASE WHEN users.date_start_vip IS NOT NULL THEN 1 ELSE 0 END) AS sum_vip
                FROM users
                WHERE deleted_at IS NULL
                UNION ALL
                SELECT
                    "song" AS model,
                    COUNT(*) AS count,
                    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) AS create_today,
                    SUM(CASE WHEN WEEK(created_at) = WEEK(NOW()) THEN 1 ELSE 0 END) AS create_week,
                    SUM(CASE WHEN MONTH(created_at) = MONTH(NOW()) THEN 1 ELSE 0 END) AS create_month,
                    NULL AS sum_vip
                FROM songs
                WHERE deleted_at IS NULL
                UNION ALL
                SELECT
                    "artist" AS model,
                    COUNT(*) AS count,
                    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) AS create_today,
                    SUM(CASE WHEN WEEK(created_at) = WEEK(NOW()) THEN 1 ELSE 0 END) AS create_week,
                    SUM(CASE WHEN MONTH(created_at) = MONTH(NOW()) THEN 1 ELSE 0 END) AS create_month,
                    NULL AS sum_vip
                FROM artists
                WHERE deleted_at IS NULL
                UNION ALL
                SELECT
                    "album" AS model,
                    COUNT(*) AS count,
                    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) AS create_today,
                    SUM(CASE WHEN WEEK(created_at) = WEEK(NOW()) THEN 1 ELSE 0 END) AS create_week,
                    SUM(CASE WHEN MONTH(created_at) = MONTH(NOW()) THEN 1 ELSE 0 END) AS create_month,
                    NULL AS sum_vip
                FROM albums
                WHERE deleted_at IS NULL
                UNION ALL
                SELECT
                    "log_listen" AS model,
                    COUNT(*) AS count,
                    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) AS create_today,
                    SUM(CASE WHEN WEEK(created_at) = WEEK(NOW()) THEN 1 ELSE 0 END) AS create_week,
                    SUM(CASE WHEN MONTH(created_at) = MONTH(NOW()) THEN 1 ELSE 0 END) AS create_month,
                    NULL AS sum_vip
                FROM log_listen
            '));

            $newResult = [];
            foreach ($result as $item) {
                $newResult[$item->model] = $item;
                unset($item->model);
            }
            return $this->renderJsonResponse($newResult, __('messages.dashboard.list.success'));
        } catch (Exception $e) {
            Log::error("[UserController][login] error because" . $e->getMessage());
            throw new Exception('[UserController][login] error because ' . $e->getMessage());
        }
    }
}
