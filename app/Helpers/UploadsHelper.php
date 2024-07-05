<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use wapmorgan\Mp3Info\Mp3Info;

class UploadsHelper
{
    /**
     * Get File Extension
     * @param $name
     * @param $request
     * @return string
     */
    public static function getFileExtension($name, $request)
    {
        if (!$request->hasFile($name)) {
            return '';
        }

        $image = $request->file($name);
        return $image->getClientOriginalExtension();
    }


    /**
     * Upload Origin File
     * @param $image
     * @param $uploadPath
     * @param $name
     * @param $request
     * @return string
     */
    public static function uploadFileOrigin($image, $uploadPath, $name, $request)
    {
        if (!$request->hasFile($name)) {
            return '';
        }

        $imageName = $image->getClientOriginalName();
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $image->move($uploadPath, $imageName);
        return '/' . $uploadPath . $imageName;
    }

    /**
     * Get Origin File
     * @param $name
     * @param $uploadPath
     * @param $request
     * @return string
     */
    public static function getOriginFile($name, $uploadPath, $request)
    {
        if (!$request->hasFile($name)) {
            return '';
        }

        $file = $request->file($name);
        return self::uploadFileOrigin($file, $uploadPath, $name, $request);
    }

    /**
     * Handle Upload Image
     * @param string $uploadPath
     * @param $name
     * @param $request
     * @return string | null
     */
    public static function handleUploadFile($uploadPath, $name, $request)
    {
        $fullPath = '';
        if (!$request->hasFile($name)) {
            return $fullPath;
        }
        $file = $request->file($name);
        if(empty($file)) return null;
//        $saveName = $file->hashName();
        $saveName = date('YmdHis') . '_' . sha1(Str::uuid()) . '.' . $file->getClientOriginalExtension();
        $fullPath = $uploadPath . $saveName;
        if (!Storage::disk()->exists($uploadPath)) {
            Storage::disk()->makeDirectory($uploadPath);
        }
        Storage::disk()->put($fullPath, file_get_contents($file));
        return $fullPath;
    }
    /**
     * Handle get duration song
     * @param string $songPath
     * @return string $duration
     */
    public static function getDurationSong($songPath)
    {
        $fullPath = storage_path('app/public/' . $songPath);
        if (!file_exists($fullPath)) {
            return '00:00';
        }
        $audio = new Mp3Info($fullPath);
        $durationInSeconds = $audio->duration;
        $minutes = floor($durationInSeconds / 60);
        $seconds = $durationInSeconds % 60;
        return sprintf("%02d:%02d", $minutes, $seconds);
    }
    /**
     * Handle Remove Image
     * @param string $uploadPath
     * @param $name
     * @param $request
     * @return string
     */
    public static function handleRemoveFile($uploadPath, $name, $request)
    {
        $fullPath = '';
        if (!$request->hasFile($name)) {
            return $fullPath;
        }

        $fileDelete = $request->get($name);
        $fullPath = $uploadPath . $fileDelete;
        if (Storage::disk()->exists($fullPath)) {
            Storage::disk()->delete($fullPath);
        }
        return $fullPath;
    }

    /**
     * Handle Remove file
     * @param $name
     * @param $request
     * @return string
     */
    public static function handleDeleteFile($fileDelete)
    {
        if(!$fileDelete) return null;
        if (Storage::disk()->exists($fileDelete)) {
            Storage::disk()->delete($fileDelete);
        }
        return $fileDelete;
    }

    /**
     * Upload temporary document file
     *
     * @param Request $request
     * @return Response
     */
    public static function uploadTemp(Request $request)
    {   
        $file = $request->file('file');
        $filePath = 'content'.'/'.Str::random(25). '.' . $file->getClientOriginalExtension();
        $directory = dirname($filePath);
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
        // Store file
        Storage::put($filePath, file_get_contents($file));
        $data = [
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath
        ];
        return $data;
    }
}
