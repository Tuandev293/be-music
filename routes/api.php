<?php

use App\Http\Controllers\Api\File\GetFileController;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Admin\AlbumController;
use App\Http\Controllers\Api\Admin\ArtistController;
use App\Http\Controllers\Api\Admin\TransactionController;
use App\Http\Controllers\Api\File\SongController;
use App\Http\Controllers\Api\User\PaymentController;
use App\Http\Controllers\Api\User\PlayListController;
use App\Http\Controllers\Api\User\SongFavoriteController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('api')->prefix('v1/')->group(function () {
    /**
     * User Group Route
     */
    Route::prefix('user/')->group(function () {
        Route::post('register', [UserController::class, 'store']);
        Route::post('verify-code', [UserController::class, 'verifyCodeRegister']);
        Route::post('login', [UserController::class, 'login']);
        Route::middleware(['auth:api', 'CheckUserLogin'])->group(function () {
            Route::get('', [UserController::class, 'getUserLogin']);
            Route::post('', [UserController::class, 'updateProfile']);
            Route::post('change-password', [UserController::class, 'updatePassword']);
            Route::post('playlist', [PlayListController::class, 'createPlayList']);
            Route::get('playlist/list', [PlayListController::class, 'listPlayList']);
            Route::get('playlist/{id}', [PlayListController::class, 'listSongInPlayList']);
            Route::post('add/song-playlist', [PlayListController::class, 'addSongPlayList']);
            Route::delete('delete/playlist/{playlistId}', [PlayListController::class, 'deletePlayList']);
            Route::post('remove/song-playlist', [PlayListController::class, 'removeSongPlayList']);
            Route::post('like-dislike/song', [SongFavoriteController::class, 'favoriteSong']);
            Route::get('favorite/song', [SongFavoriteController::class, 'getFavoriteSong']);
            Route::post('payment', [PaymentController::class, 'requestPayment']);
            Route::get('download', [UserController::class, 'downloadSong']);
        });
    });

    /**
     * Song Public Group Route
     */
    Route::prefix('song/')->group(function () {
        Route::get('list', [SongController::class, 'list']);
        Route::get('list-popular', [SongController::class, 'listPopular']);
        Route::get('list-category', [SongController::class, 'listCategory']);
    });

    /**
     * Artist Public Group Route
     */
    Route::prefix('artist/')->group(function () {
        Route::get('list', [ArtistController::class, 'list']);
        Route::get('detail', [ArtistController::class, 'artistDetail']);
    });

    /**
     * Artist Public Group Route
     */
    Route::prefix('album/')->group(function () {
        Route::get('list', [AlbumController::class, 'list']);
        Route::get('list-song', [AlbumController::class, 'listSong']);
    });

    /**
     * Admin Group Route
     */
    Route::prefix('admin/')->group(function () {
        Route::post('login', [AdminController::class, 'login']);
        Route::middleware(['auth:admin', 'CheckAdminLogin'])->group(function () {
            Route::post('create-song', [SongController::class, 'store']);
            Route::get('dashboard', [AdminController::class, 'getDashboard']);
            Route::post('update-song/{song_id}', [SongController::class, 'update']);
            Route::post('update-artist/{artist_id}', [ArtistController::class, 'update']);
            Route::post('update-album/{album_id}', [AlbumController::class, 'update']);
            Route::delete('delete-song', [SongController::class, 'delete']);
            Route::delete('delete-album', [AlbumController::class, 'delete']);
            Route::delete('delete-artist', [ArtistController::class, 'delete']);
            Route::get('/info', [AdminController::class, 'getUserLogin']);
            Route::post('create-artist', [ArtistController::class, 'store']);
            Route::post('create-album', [AlbumController::class, 'store']);
            Route::get('list-transaction', [TransactionController::class, 'list']);
        });
    });
    Route::get('/file', [GetFileController::class, 'getFile'])->name('api.getFile');
    Route::post('/payment/momo-return', [PaymentController::class, 'momoCallback'])->name('api.momo.return');
});