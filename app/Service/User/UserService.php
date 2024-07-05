<?php

namespace App\Service\User;

use App\Common\Common;
use App\Helpers\UploadsHelper;
use App\Jobs\SendEmailRegisterComplete;
use App\Models\Activation;
use App\Models\GuardUtils;
use App\Models\LogListen;
use App\Models\SongFavorite;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{
    /**
     * @var Common
     */
    private $common;

    public function __construct(
        Common $common
    ) {
        $this->common = $common;
    }
    /**
     * Store User
     * @param $request
     * @return mixed
     * @throws Exception
     */
    public function storeUser($request)
    {
        try {
            DB::beginTransaction();
            $data = [
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'avatar' =>  config('constants.userAvatarDefault'),
                'email' => $request->input('email'),
                'gender' => $request->input('gender'),
                'password' => Hash::make($request->input('password')),
                'status' => User::INACTIVE,
            ];

            $user = User::create($data);
            $code = $this->common->storeActivetion($user->id);
            if (!empty($user->email) && !empty($code)) {
                dispatch(new SendEmailRegisterComplete($user->email, $code, $user->name))->onQueue(config('queue.queueType.mail'));
            }
            DB::commit();

            return $user;
        } catch (Exception $e) {
            Log::error('[UserService][storeUser] error ' . $e->getMessage());
            DB::rollBack();
            throw new Exception('[UserService][storeUser] error ' . $e->getMessage());
        }
    }

    /**
     * Verify Register
     * @param $request
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function verifyRegister($request)
    {
        $id = $request->input('user_id');
        $needRollback = false;
        try {
            $data = User::join('activations', 'activations.user_id', 'users.id')
                ->where('activations.user_id', $id)
                ->where('activations.code', $request->input('code'))
                ->where('activations.expired_time', '>=', Carbon::now()->format('Y-m-d H:i:s'))
                ->where('activations.completed', Activation::COMPLETED_FALSE)
                ->where('users.status', User::INACTIVE)
                ->select(['users.*', 'activations.id as activation_id'])
                ->first();
            if (empty($data)) {
                return null;
            }
            DB::beginTransaction();
            $needRollback = true;
            User::where('id', $id)->update([
                'status' => User::ACTIVE,
            ]);
            Activation::where('id', $data->activation_id)->update([
                'completed' => Activation::COMPLETED_TRUE,
                'completed_at' => Carbon::now(),
            ]);
            $accessToken = $data->createToken('AuthToken User', ['user'])->accessToken;
            $data->accessToken = $accessToken;
            DB::commit();
            return $data;
        } catch (Exception $e) {
            Log::error('[UserService][verifyRegister] error ' . $e->getMessage());
            if ($needRollback) {
                DB::rollBack();
            }
            throw new Exception('[UserService][verifyRegister] error ' . $e->getMessage());
        }
    }

    /**
     * Login Account Use By Email
     * @param $request
     * @return array|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object | null
     * @throws Exception
     */
    public function loginByAccount($request)
    {
        $email = $request->input('email');
        try {
            $guard = User::query()
                ->where('email', $email)
                ->select([
                    'users.*',
                ])
                ->first();

            if (empty($guard) || !Hash::check($request->input('password'), $guard->password)) {
                return null;
            }
            $accessToken = $guard->createToken('AuthToken User', ['user'])->accessToken;

            $guard->accessToken = $accessToken;
            $guard->is_vip = !empty($guard->date_start_vip) ? User::VIP : User::NOT_VIP;
            if ($guard->status == User::INACTIVE) {
                DB::beginTransaction();
                $code = $this->common->storeActivetion($guard->id);
                DB::commit();
                if (!empty($guard->email) && !empty($code)) {
                    dispatch(new SendEmailRegisterComplete($guard->email, $code, $guard->name))->onQueue(config('queue.queueType.mail'));
                }
                $data = [
                    'id' => $guard->id,
                    'username' => $guard->username,
                    'name' => $guard->name,
                    'birthday' => $guard->birthday,
                    'gender' => $guard->gender,
                    'cover_image'=> $guard->cover_image,
                    'phone'=> $guard->phone,
                    'address' => !empty($guard->address) ? $guard->address : '',
                    'prefecture_name' => !empty($guard->prefecture_name) ? $guard->prefecture_name : '',
                    'district_name' => !empty($guard->district_name) ? $guard->district_name : '',
                    'flag' => User::INACTIVE,
                    'type' => GuardUtils::USER,
                    'code' => $code->code
                ];
                $guard = $data;
            }
            return $guard;
        } catch (Exception $e) {
            Log::error('[UserService][loginByAccount] error ' . $e->getMessage());
            DB::rollBack();
            throw new Exception('[UserService][loginByAccount] error ' . $e->getMessage());
        }
    }

    /**
     * get favorite song of user
     * @param $userId
     * @param $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws Exception
     */
    public function getFavoriteSong($userId, $request)
    {
        $perPage = is_numeric($request->get('per_page')) ? (int) $request->get('per_page') : config('constants.perPage');
        $subQuery = LogListen::query()->select([DB::raw('COUNT(log_listen.id) as total_listen'), 'song_id as listen_song_id'])->groupBy('log_listen.song_id');
        $data = SongFavorite::where('user_id', $userId)
            ->join('songs', function ($qr) {
                $qr-> on('song_favorites.song_id', 'songs.id')
                    ->whereNull('songs.deleted_at');
            })
            ->leftJoinSub($subQuery, 'sub_query','songs.id', '=', 'sub_query.listen_song_id')
            ->leftJoin('song_category', 'songs.category_id', 'song_category.id')
            ->leftJoin('albums', 'songs.album_id', 'albums.id')
            ->leftJoin('artists', 'songs.artist_id', 'artists.id')
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
                DB::raw('1 as favorite'),
                DB::raw('IFNULL(sub_query.total_listen, 0) as total_listen'),
                DB::raw('DATE_FORMAT(songs.created_at, "%d-%m-%Y") as date_create')
            ])
            ->paginate($perPage);
        return $data;
    }

    /**
     * @param  $request
     * @return array
     * @throws Exception
     */
    public function updateProfile($request)
    {
        try {
            $user = Auth::guard('api')->user();
            $pathAvatar = $request->input('avatar');
            $pathCover = $request->input('cover_image');
            if(is_file($request->file('avatar'))){
                $pathAvatar = UploadsHelper::handleUploadFile(config('constants.userAvatar'), 'avatar', $request);
            }
            if(is_file($request->file('cover_image'))){
                $pathCover = UploadsHelper::handleUploadFile(config('constants.userCover'), 'cover_image', $request);
            }
            $param = $request->only('birthday', 'gender', 'name', 'phone');
            $param['avatar'] = $pathAvatar;
            $param['cover_image'] = $pathCover;
            User::where('id', $user->id)->update($param);
            $updatedUser = User::find($user->id);
            $updatedUser->is_vip = !empty($updatedUser->date_end_vip) && !empty($updatedUser->date_start_vip) ? User::VIP : User::NOT_VIP;
            return $updatedUser;
        } catch (Exception $e) {
            Log::error("[UserService][updateProfile] error because" . $e->getMessage());
            throw new Exception('[UserService][updateProfile] error because ' . $e->getMessage());
        }
    }

    /**
     * @param  $request
     * @return \Illuminate\Contracts\Auth\Authenticatable | null
     * @throws Exception
     */
    public function updatePassword($request)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!Hash::check($request->input('password_old'), $user->password)) {
                return null;
            }
            $user->password = Hash::make($request->input('password'));
            $user->save();
            return $user;
        } catch (Exception $e) {
            Log::error("[UserService][updateProfile] error because" . $e->getMessage());
            throw new Exception('[UserService][updateProfile] error because ' . $e->getMessage());
        }
    }
}
