<?php

namespace App\Http\Middleware;

use Beetech\Api\Http\Controllers\AbstractApiController;
use Beetech\Core\Helpers\Common;
use Beetech\Api\Services\CheckVersionApp\CheckVersionApp;
use Beetech\Core\Models\VersionApp;
use Illuminate\Http\Request;
use Closure;


class CheckAppVersion
{
    protected $response;
    protected $version;

    public function __construct(AbstractApiController $response, CheckVersionApp $version)
    {
        $this->response = $response;
        $this->version = $version;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle(Request $request, Closure $next)
    {
        $deviceId = $request->headers->get('device');
        $version = $request->headers->get('version');
        $device = config('constants.arrOneTwo');
        if ($deviceId == config('constants.deviceIdWeb')) {
            return $next($request);
        }
        if (!($deviceId && $version && in_array($deviceId, $device))) {
            return $this->response->respondServiceUnavailable();
        }
        $lastVersion = $this->version->lastVersion($deviceId);
        if (!$lastVersion || Common::checkAppVersion($lastVersion->name, $version)) {
            return $next($request);
        }
        return $this->response->respondServiceUnavailable();
    }
}
