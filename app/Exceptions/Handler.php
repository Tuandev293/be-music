<?php

namespace App\Exceptions;

use Throwable;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Illuminate\Http\Client\RequestException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use App\Common\Common;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Foundation\Application;

class Handler extends ExceptionHandler
{
        /**
     *
     * @var Application
     */
    protected $app;
    public function __construct(Application $app) {
        $this->app = $app;
    }
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        ConnectException::class,
        RequestException::class,
        Exception::class
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];
    /**
     * @param \Illuminate\Http\Request $request
     * @param Throwable $e
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($request->is('api/*')) {
            if (!$e instanceof ValidationException && strlen(Common::trimSpaces($e->getMessage()) ) > 0 ) {
                Log::error('Error system ' . $e->getMessage());
            }
            return $this->renderErrorApi($request, $e);
        }
        return parent::render($request, $e);
    }
    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

        /**
     * @param $request
     * @param $exception
     * @return \Illuminate\Http\JsonResponse
     */
    private function renderErrorApi($request, $exception)
    {
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        $msg = __('messages.common.serverError');
        $response = [
            'code' => $statusCode,
            'msg' => $msg
        ];
        
        //server error
        if ($exception instanceof Exception) {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $msg = __('messages.common.serverError');
            $response = [
                'code' => $statusCode,
                'msg' => $msg,
            ];
        }

        if ($exception instanceof AuthenticationException)
        {
            $statusCode = Response::HTTP_UNAUTHORIZED;
            $response = [
                'code' => Response::HTTP_UNAUTHORIZED,
                'msg' => __('messages.common.unauthenticated'),
            ];
        }

        if ($exception instanceof ValidationException)
        {
            $msg =  __('messages.common.unauthenticated');
            $flagError = 0;
            foreach ($exception->errors() as $field => $errors) {
                foreach ($errors as $key => $value) {
                    $msg = $value;
                    $flagError++;
                    continue;
                }
                if($flagError > 1) {
                    break;
                }
            }
            $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
            $response = [
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'msg' => $msg,
            ];
        }

        if (
            $exception instanceof ModelNotFoundException ||
            $exception instanceof NotFoundHttpException ||
            $exception instanceof MethodNotAllowedHttpException
        )
        {
            $statusCode = Response::HTTP_NOT_FOUND;
            $msg = __('messages.common.notFound');
            $response = [
                'code' => Response::HTTP_NOT_FOUND,
                'msg' => $msg,
            ];
        }

        //time out
        if ($exception instanceof ConnectException)
        {
            $statusCode = Response::HTTP_REQUEST_TIMEOUT;
            $response = [
                'code' => Response::HTTP_REQUEST_TIMEOUT,
                'msg' => __('messages.common.request_timeout'),
            ];
        }

        //no content
        if ($exception instanceof RequestException) {
            if (empty($exception->response)) {
                $statusCode = Response::HTTP_NO_CONTENT;
                $response = [
                    'code' => Response::HTTP_NO_CONTENT,
                ];
            }
        }

        //maintenance
        if($this->app->isDownForMaintenance()){
            $statusCode = Response::HTTP_SERVICE_UNAVAILABLE;
            $response = [
                'code' => Response::HTTP_SERVICE_UNAVAILABLE,
                'msg' => __('messages.common.new_version_updating'),
            ];
        }
        return response()->json($response, $statusCode);
    }
}
