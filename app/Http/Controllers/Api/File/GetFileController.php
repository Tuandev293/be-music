<?php
namespace App\Http\Controllers\Api\File;
use App\Core\AbstractApiController;
use App\Jobs\WriteLogListen;
use App\Models\File;
use App\Service\File\UploadFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GetFileController extends AbstractApiController{
    protected $uploadFile;

    public function __construct(UploadFileService $uploadFile)
    {
        $this->uploadFile = $uploadFile;
    }
    /**
     * get file
     * @param \Illuminate\Http\Request $request
     */
    public function getFile(Request $request){
        $filePath = $request->get('filePath');
        if (!isset($request->filePath) || !Storage::disk()->exists($filePath)) {
            return $this->respondNotFound(__('messages.file.notFound'));
        }
        
        $infoPath = pathinfo(Storage::disk()->path($filePath, PATHINFO_EXTENSION));
        $extension = $infoPath['extension'];
        $typeFile = $this->uploadFile->checkExtensionUpload($extension);
        if($typeFile == File::EXTENSION_VIDEO || $typeFile == File::EXTENSION_RADIO){
            $clientIp = $request->ip();
            if($clientIp){
                dispatch(new WriteLogListen($clientIp, $filePath))->onQueue(config('queue.queueType.listen'));
            }
            $rangeHeader = request()->header('Range');
            $fileContents = Storage::disk()->get($filePath);
            // $fullFilePath = config('constants.sftpPath') . Storage::path($filePath); //https://stackoverflow.com/a/49532280/470749
            $headers = ['Content-Type' => Storage::mimeType($filePath)];
            if ($rangeHeader) {
                return self::getResponseStream('', $filePath, $fileContents, $rangeHeader, $headers);
            } else {
                $httpStatusCode = 200;
                return response($fileContents, $httpStatusCode, $headers);
            }
        }
        return Storage::disk()->response($request->get('filePath'));
    }
    
    /**
     * 
     * @param string $disk
     * @param string $fullFilePath
     * @param string $fileContents
     * @param string $rangeRequestHeader
     * @param array  $responseHeaders
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public static function getResponseStream($disk, $fullFilePath, $fileContents, $rangeRequestHeader, $responseHeaders) {
        $stream = Storage::disk($disk)->readStream($fullFilePath);
        $fileSize = strlen($fileContents);
        $fileSizeMinusOneByte = $fileSize - 1; //because it is 0-indexed. https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.16
        list($param, $rangeHeader) = explode('=', $rangeRequestHeader);
        if (strtolower(trim($param)) !== 'bytes') {
            abort(400, "Invalid byte range request"); //Note, this is not how https://stackoverflow.com/a/29997555/470749 did it
        }
        list($from, $to) = explode('-', $rangeHeader);
        if ($from === '') {
            $end = $fileSizeMinusOneByte;
            $start = $end - intval($from);
        } elseif ($to === '') {
            $start = intval($from);
            $end = $fileSizeMinusOneByte;
        } else {
            $start = intval($from);
            $end = intval($to);
        }
        $length = $end - $start + 1;
        $httpStatusCode = 206;
        $responseHeaders['Content-Range'] = sprintf('bytes %d-%d/%d', $start, $end, $fileSize);
        $responseStream = response()->stream(function() use ($stream, $start, $length) {
            fseek($stream, $start, SEEK_SET);
            echo fread($stream, $length);
            fclose($stream);
        }, $httpStatusCode, $responseHeaders);
        return $responseStream;
    }
}

?>