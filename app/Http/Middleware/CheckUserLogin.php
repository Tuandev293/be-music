<?php

namespace App\Http\Middleware;

use App\Core\AbstractApiController;
use Closure;
use Illuminate\Http\Request;

class CheckUserLogin
{
    protected $response;

    public function __construct(AbstractApiController $response)
    {
        $this->response = $response;
    }
    /**
     * Handle check request token user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return  \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()->tokenCan('user')) {
            return $this->response->respondUnauthorized() ;
        }
        return $next($request);
    }
}
