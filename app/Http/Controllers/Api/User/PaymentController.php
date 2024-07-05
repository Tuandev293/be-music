<?php

namespace App\Http\Controllers\Api\User;

use App\Core\AbstractApiController;
use App\Models\LogTransactionMoMo;
use App\Models\User;
use App\Service\User\PaymentService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends AbstractApiController
{
    protected $paymentService;

    /**
     * @param PaymentService $paymentService
     */
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Handle the payment for a schedule.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function requestPayment(Request $request)
    {
        try {
            if($request->input("type_payment") == LogTransactionMoMo::THREE_DAY) {
                $userId = Auth::guard('api')->user()->id;
                $endVip = Carbon::now()->addDays(6)->format('Y-m-d');
                $today = Carbon::now()->format('Y-m-d');
                $userCheck = User::where('id', $userId)
                    ->where('is_use_free', User::HAVE_NOT_FREE)
                    ->update(['date_start_vip'=> $today, 'date_end_vip' => $endVip, 'is_use_free'=> User::HAVE_USE_FREE]);
                if(!empty($userCheck)) {
                    return $this->respondCreated([], __('messages.payment.create.success_free'));
                }
                return $this->respondBadRequest(__('messages.payment.create.fail_free'));
            }
            $payment = $this->paymentService->createPayment($request);
            if(empty($payment)) {
                return $this->respondBadRequest(__('messages.payment.create.fail'));
            }
            return $this->renderJsonResponse($payment, __('messages.payment.create.success'));
        } catch (Exception $e) {
            Log::error("[UserFileController][paymentSchedule] error " . $e->getMessage());
            throw new Exception('[UserFileController][paymentSchedule] error ' . $e->getMessage());
        }
    }

    /**
     * Handle the payment for a schedule.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     * @throws \Exception
     */
    public function momoCallback(Request $request)
    {
        try {
            $result = $this->paymentService->handleCallbackPayment($request);
            if (!$result) {
                return response(null, Response::HTTP_BAD_REQUEST);
            }
            return response(null, Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            Log::error("[PaymentController][momoCallback] error " . $e->getMessage());
            throw new Exception('[PaymentController][momoCallback] error ' . $e->getMessage());
        }
    }
}
