<?php

namespace App\Http\Controllers\Api\Admin;

use App\Core\AbstractApiController;
use App\Service\User\PaymentService;
use Exception;
use Illuminate\Http\Request;


class TransactionController extends AbstractApiController
{
    protected $paymentService;
    public function __construct(PaymentService $paymentService = null) {
        $this->paymentService = $paymentService;
    }
    /**
     * get list transaction
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function list(Request $request)
    {
        try {
            $albums = $this->paymentService->getTransaction($request);
            return $this->respondWithPagination($albums, __('messages.transaction.list.success'));
        } catch (Exception $e) {
            throw new Exception('[TransactionController][list] error because ' . $e->getMessage());
        }
    }
}
