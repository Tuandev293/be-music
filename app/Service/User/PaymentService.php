<?php

namespace App\Service\User;

use App\Models\LogTransactionMoMo;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Create payment momo
     *
     * @param $request
     * @throws Exception
     */
    public function createPayment($request)
    {
        try {
            $user = Auth::guard('api')->user();
            if(!empty($user->date_start_vip)) {
                return null;
            }
            $userId = $user->id;
            $amount = $this->getAmount($request->input('type_payment'));
            $type_payment = $request->input('type_payment');
            if (empty($amount)) return null;
            Log::info("[PaymentService][createPaymentMomo] Start payment with Momo by user $userId");
            $endpoint =  config('constants.momoBaseUrl') . 'create';
            $partnerCode = config('constants.momoPartnerCode');
            $accessKey = config('constants.momoAccessKey');
            $secretKey = config('constants.momoSecretKey');
            $redirectUrl = config('constants.redirectUrl'); //route navigation when payment is complete
            $ipnUrl = config('constants.ipnUrl'); //route Momo returns transaction results
            $orderId = time() . ":" . $userId;
            $extraData = base64_encode(json_encode(['user_id' => $userId, 'type_payment' => $type_payment]));
            $requestId = time() . "id";
            $requestType = "captureWallet";
            $orderInfo = "Thanh toán qua MoMo";
            $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
            $signature = hash_hmac("sha256", $rawHash, $secretKey);
            $data = array(
                'partnerCode' => $partnerCode,
                'requestId' => $requestId,
                'amount' => $amount,
                'orderId' => $orderId,
                'orderInfo' => $orderInfo,
                'redirectUrl' => $redirectUrl,
                'ipnUrl' => $ipnUrl,
                'lang' => 'vi',
                'extraData' => $extraData,
                'requestType' => $requestType,
                'signature' => $signature,
            );
            // Send payment request to Momo
            $client = new Client();
            $response = $client->post($endpoint, [
                'json' => $data,
            ]);
            $responseData = json_decode($response->getBody(), true);
            Log::info("[PaymentService][createPaymentMomo] Reponse data payment Momo with user id $userId, data: $ipnUrl" . json_encode($responseData));
            if (empty($responseData['errorCode'])) {
                $responseData = [
                    'amount' => $responseData['amount'],
                    'payUrl' => $responseData['payUrl'],
                    'deeplink' =>  $responseData['deeplink']
                ];
                Log::info("[PaymentService][createPaymentMomo] End payment with Momo by user $userId");
                return $responseData;
            }
            return null;
        } catch (Exception $e) {
            Log::error("[PaymentService][createPaymentMomo] error because" . $e->getMessage());
            throw new Exception('[PaymentService][createPaymentMomo] error because ' . $e->getMessage());
        }
    }

    /**
     * Handle payment when momo return
     *
     * @param $request
     * @throws Exception
     */
    public function handleCallbackPayment($request)
    {
        try {
            Log::info("[PaymentService][handleCallbackPayment] Start call back payment from momo");
            $data = [
                "partnerCode" => $request->input('partnerCode'),
                "orderId" => $request->input('orderId'),
                "requestId" => $request->input('requestId'),
                "amount" => $request->input('amount'),
                "orderInfo" => $request->input('orderInfo'),
                "orderType" => $request->input('orderType'),
                "transId" => $request->input('transId'),
                "resultCode" => $request->input('resultCode'),
                "message" => $request->input('message'),
                "payType" => $request->input('payType'),
                "responseTime" => $request->input('responseTime'),
                "extraData" => $request->input('extraData'),
                "signature" => $request->input('signature')
            ];
            Log::info("[PaymentService][handleCallbackPayment] Data Momo response" . json_encode($data));
            $checkSignature = $this->verifySignature($data);
            Log::info("[PaymentService][handleCallbackPayment] Callback valid signature");
            if ($checkSignature) {
                $paymentData = json_decode(base64_decode($data['extraData']));
                $userId = $paymentData->user_id;
                $type_payment = $paymentData->type_payment;
                if ($request->input('resultCode') == LogTransactionMoMo::PAYMENT_SUCCESS_STATUS) {
                    if (!empty($paymentData) && in_array($type_payment, [LogTransactionMoMo::ONE_MONTH, LogTransactionMoMo::SIX_MONTH, LogTransactionMoMo::ONE_YEAR])) {
                        $user = User::findOrFail($userId);
                        DB::beginTransaction();
                        LogTransactionMoMo::create([
                            'user_id' => $user->id,
                            'order_id' => $data['orderId'],
                            'amount' => $data['amount'],
                            'message' => $data['message'],
                            'trans_id' => $data['transId'],
                            'status' => LogTransactionMoMo::IS_PAID
                        ]);
                        // Update payment status to paid
                        $today = Carbon::now()->format('Y-m-d');
                        User::where('id', $userId)
                        ->update(['date_start_vip'=> $today, 'date_end_vip' => $this->getDateEndVip($type_payment)]);
                        DB::commit();
                    }
                }
                Log::info("[PaymentService][handleCallbackPayment] End call back payment from momo");
                return true;
            }
            return false;
        } catch (Exception $e) {
            Log::error("[MomoPaymentController][handleCallbackPayment] error " . $e->getMessage());
            DB::rollBack();
            throw new Exception('[MomoPaymentController][handleCallbackPayment] error ' . $e->getMessage());
        }
    }
    /**
     * Verifies the signature of the data based on the provided signature.
     *
     * @param array $data An array containing the data to be verified.
     * @return bool Returns true if the signature is valid, false otherwise.
     */
    public function verifySignature($data)
    {
        $accessKey = config('constants.momoAccessKey');
        $partnerCode = config('constants.momoPartnerCode');
        [
            'amount' => $amount,
            'extraData' => $extraData,
            'message' => $message,
            'orderId' => $orderId,
            "orderInfo" => $orderInfo,
            "orderType" => $orderType,
            "payType" => $payType,
            "requestId" => $requestId,
            "responseTime" => $responseTime,
            "resultCode" => $resultCode,
            "transId" => $transId,
            "signature" => $signature
        ] = $data;

        //không format xuống dòng ở đây vì sẽ làm mã hóa sai
        $signatureRaw = "accessKey=$accessKey&amount=$amount&extraData=$extraData&message=$message&orderId=$orderId&orderInfo=$orderInfo&orderType=$orderType&partnerCode=$partnerCode&payType=$payType&requestId=$requestId&responseTime=$responseTime&resultCode=$resultCode&transId=$transId";
        $newSignature = hash_hmac("sha256", $signatureRaw, config('constants.momoSecretKey'));
        return hash_equals($newSignature, $signature);
    }

    /**
     * Get amount by type
     *
     * @param int $type 
     * @return int
     */
    public function getAmount($type)
    {
        $amount = 0;
        if ($type == LogTransactionMoMo::ONE_MONTH) $amount = 30000;
        if ($type == LogTransactionMoMo::SIX_MONTH) $amount = 150000;
        if ($type == LogTransactionMoMo::ONE_YEAR) $amount = 240000;
        return $amount;
    }
    /**
     * Get date end vip by type
     *
     * @param int $type 
     * @return string Returns true if the signature is valid, false otherwise.
     */
    public function getDateEndVip($type)
    {
        $date = 0;
        if ($type == LogTransactionMoMo::ONE_MONTH) $date = Carbon::now()->addMonth();
        if ($type == LogTransactionMoMo::SIX_MONTH) $date = Carbon::now()->addMonths(6);
        if ($type == LogTransactionMoMo::ONE_YEAR) $date = Carbon::now()->addYear();
        return $date->format('Y-m-d');
    }

    /**
     * Get list transaction
     *
     * @param int $type 
     * @return string Returns true if the signature is valid, false otherwise.
     */
    public function getTransaction($request)
    {
        $perPage = is_numeric($request->get('per_page')) ? (int) $request->get('per_page') : config('constants.perPage');
        $transactions = LogTransactionMoMo::query()
                            ->join('users', 'log_transaction_momo.user_id', 'users.id')
                            ->select([
                                'log_transaction_momo.id as transaction_id',
                                'status',
                                'users.name as user_name',
                                'message',
                                DB::raw('DATE_FORMAT(log_transaction_momo.created_at, "%d-%m-%Y") as create_at'),
                                DB::raw('CONCAT(IFNULL(REPLACE(FORMAT(SUM(branch_test_cat_medical.price), 0), ",", "."), 0), " VNĐ") as amount')
                            ])->paginate($perPage);
        return $transactions;
    }
}
