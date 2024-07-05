<?php
namespace App\Service\File;
use App\Models\File;

class UploadFileService
{

    /**
    * check extension upload file
    * @param $extension
    * @return int
    * @throws Exception
    */

    public function checkExtensionUpload($extension)
    {
        switch (true) {
            case in_array(strtolower($extension), ['jpeg', 'png', 'jpg', 'gif', 'svg', 'tiff']):
                $result = File::EXTENSION_IMAGE;
                break;
            case in_array(strtolower($extension), ['mov', 'mp4']):
                $result = File::EXTENSION_VIDEO;
                break;
            case in_array(strtolower($extension), ['mp3', 'm4a']):
                $result = File::EXTENSION_RADIO;
                break;
            default:
                $result = File::EXTENSION_FILE_OTHER;
                break;
        }
        return $result;
    }
}
