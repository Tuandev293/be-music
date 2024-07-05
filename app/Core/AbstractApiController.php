<?php

namespace App\Core;

use App\Http\Controllers\Controller;
use \Illuminate\Http\Response as Res;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class AbstractApiController
 *
 * An abstract controller class for building API responses.
 *
 * @package App\Core
 */
class AbstractApiController extends Controller
{
    protected $guard = 'api';
    /**
     * @var int
     */
    protected $statusCode = Res::HTTP_OK;

    const HTTP_LISTENER = 209;

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard($this->guard);
    }

    /**
     * Get the current status code.
     *
     * @return int The current status code.
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Set the status code for the response.
     *
     * @param int $statusCode The status code to be set.
     * @return $this The instance of this class.
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Generate a JSON response.
     *
     * @param mixed $data The data to be included in the JSON response.
     * @param array $headers Additional headers to be included in the response.
     * @return \Illuminate\Http\JsonResponse The generated JSON response.
     */
    public function respond($data, $headers = [])
    {
        return response()->json($data, $this->getStatusCode(), $headers);
    }

    /**
     * Generate a JSON response for a created resource.
     *
     * @param mixed $data The data to be included in the JSON response.
     * @param string|null $msg The custom message for the response.
     * @return \Illuminate\Http\JsonResponse The generated JSON response.
     */
    public function respondCreated($data = null, $msg = null)
    {
        $statusCode = Res::HTTP_CREATED;
        $this->setStatusCode($statusCode);
        $response = [];
        $response['code'] = $statusCode;
        $response['msg'] = __('api::messages.common.success');
        if ($msg) $response['msg'] = $msg;
        if ($data) $response['data'] = $data;
        return $this->respond($response);
    }

    /**
     * Generate a JSON response for an updated resource.
     *
     * @param mixed $data The data to be included in the JSON response.
     * @param string|null $msg The custom message for the response.
     * @return \Illuminate\Http\JsonResponse The generated JSON response.
     */
    public function respondUpdated($data = null, $msg = null)
    {
        $response = [];
        $response['code'] = $this->getStatusCode();
        $response['msg'] = __('api::messages.common.success');
        if ($msg) $response['msg'] = $msg;
        if ($data) $response['data'] = $data;
        return $this->respond($response);
    }

    /**
     * Generate a JSON response for a deleted resource.
     *
     * @param string|null $msg The custom message for the response.
     * @return \Illuminate\Http\JsonResponse The generated JSON response.
     */
    public function respondDeleted($msg = null)
    {
        $response = [];
        $statusCode = Res::HTTP_NO_CONTENT;
        $this->setStatusCode($statusCode);
        $response['code'] = $statusCode;
        $response['msg'] = __('api::messages.common.success');
        if ($msg) $response['msg'] = $msg;
        return $this->respond($response);
    }

    /**
     * Generate a JSON response for a not found resource.
     *
     * @param string $msg The custom message for the response.
     * @return \Illuminate\Http\JsonResponse The generated JSON response.
     */
    public function respondNotFound($msg)
    {
        $response = [];
        $statusCode = Res::HTTP_NOT_FOUND;
        $this->setStatusCode($statusCode);
        $response['code'] = $statusCode;
        $response['msg'] = __('api::messages.common.notFound');
        if ($msg) {
            $response['msg'] = $msg;
        }
        return $this->respond($response);
    }

    /**
     * Generate a JSON response for a forbidden request.
     *
     * @param string $msg The custom message for the response.
     * @return \Illuminate\Http\JsonResponse The generated JSON response.
     */
    public function respondForbidden($msg)
    {
        $response = [];
        $statusCode = Res::HTTP_FORBIDDEN;
        $this->setStatusCode($statusCode);
        $response['code'] = $statusCode;
        $response['msg'] = __('api::messages.common.success');
        if ($msg) {
            $response['msg'] = $msg;
        }
        return $this->respond($response);
    }

    /**
     * Generate a JSON response for a validation error.
     *
     * @param string $msg The custom message for the response.
     * @param array $errors The validation errors to be included in the response.
     * @return \Illuminate\Http\JsonResponse The generated JSON response.
     */
    public function respondValidationError($msg, $errors)
    {
        $response = [];
        $statusCode = Res::HTTP_UNPROCESSABLE_ENTITY;
        $this->setStatusCode($statusCode);
        $response['code'] = $statusCode;
        $response['msg'] = __('api::messages.common.success');
        $response['data'] = $errors;
        return $this->respond($response);
    }

    /**
     * Generate a JSON response for various scenarios.
     *
     * @param array $data The data to be included in the JSON response.
     * @param string $msg The custom message for the response.
     * @param int $status The status code for the response.
     * @return \Illuminate\Http\JsonResponse The generated JSON response.
     */
    public function renderJsonResponse($data = [], $msg = '', $status = Res::HTTP_OK)
    {
        $response = [];
        $this->setStatusCode($status);
        $response['code'] = $status;
        if (!$msg) {
            $response['msg'] = __('api::messages.common.success');
        } else {
            $response['msg'] = $msg;
        }
        $response['data'] = $data;
        return $this->respond($response);
    }

    /**
     * Generate a JSON response with pagination information.
     *
     * @param mixed $data The data to be included in the JSON response.
     * @param string $msg The custom message for the response.
     * @param int $status The status code for the response.
     * @return \Illuminate\Http\JsonResponse The generated JSON response.
     */
    public function respondWithPagination($data = [], $msg = '', $status = Res::HTTP_OK)
    {
        $response = [];
        $this->setStatusCode($status);
        $response['code'] = $status;
        if (!$msg) {
            $response['msg'] = __('api::messages.common.success');
        } else {
            $response['msg'] = $msg;
        }
        $response['current_page'] = $data->currentPage();
        $response['total_page'] = $data->lastPage();
        $response['per_page'] = $data->perPage();
        $response['total'] = $data->total();
        $response['data'] = $data->items();
        return $this->respond($response);
    }

    /**
     * Generate a JSON response for an error scenario.
     *
     * @param string $msg The error message for the response.
     * @return \Illuminate\Http\JsonResponse The generated JSON response.
     */
    public function respondWithError($msg)
    {
        $response = [];
        $statusCode = Res::HTTP_PRECONDITION_FAILED;
        $this->setStatusCode($statusCode);
        $response['code'] = $statusCode;
        $response['msg'] = $msg;
        return $this->respond($response);
    }

    /**
     * Generate a JSON response for an internal server error.
     *
     * @param string $msg The custom message for the response.
     * @return \Illuminate\Http\JsonResponse The generated JSON response.
     */
    public function respondInternalError($msg = '')
    {
        $response = [];
        $statusCode = Res::HTTP_INTERNAL_SERVER_ERROR;
        $this->setStatusCode($statusCode);
        $response['code'] = $statusCode;
        $response['msg'] = $msg;
        return $this->respond($response);
    }

    /**
     * Generate a JSON response for a bad request.
     *
     * @param string $msg The custom message for the response.
     * @return \Illuminate\Http\JsonResponse The generated JSON response.
     */
    public function respondBadRequest($msg = '')
    {
        $response = [];
        $statusCode = Res::HTTP_BAD_REQUEST;
        $this->setStatusCode($statusCode);
        $response['code'] = $statusCode;
        $response['msg'] = $msg;
        return $this->respond($response);
    }

    /**
     * Generate a JSON response for an unauthorized request.
     *
     * @return \Illuminate\Http\JsonResponse The generated JSON response.
     */
    public function respondUnauthorized()
    {
        $response = [];
        $statusCode = Res::HTTP_UNAUTHORIZED;
        $this->setStatusCode($statusCode);
        $response['code'] = $statusCode;
        $response['msg'] = __('api::messages.common.unauthenticated');
        return $this->respond($response);
    }

    /**
     * Generate a JSON response for a service being unavailable.
     *
     * @param string $msg The custom message for the response.
     * @return \Illuminate\Http\JsonResponse The generated JSON response.
     */
    public function respondServiceUnavailable($msg = '')
    {
        $response = [];
        $statusCode = Res::HTTP_VERSION_NOT_SUPPORTED;
        $this->setStatusCode($statusCode);
        $response['code'] = $statusCode;
        $response['msg'] = __('api::messages.common.new_version');
        if ($msg) {
            $response['msg'] = $msg;
        }
        return $this->respond($response);
    }

    /**
     * Generate a JSON response for a call to a non-existent listener.
     *
     * @param mixed $data The data to be included in the JSON response.
     * @param string $msg The custom message for the response.
     * @return \Illuminate\Http\JsonResponse The generated JSON response.
     */
    public function respondCallNoListener($data, $msg = '')
    {
        $response = [];
        $statusCode = self::HTTP_LISTENER;
        $this->setStatusCode($statusCode);
        $response['code'] = $statusCode;
        $response['msg'] = __('api::messages.common.new_version');
        if ($msg) {
            $response['msg'] = $msg;
        }
        $response['data'] = $data;
        return $this->respond($response);
    }

}
