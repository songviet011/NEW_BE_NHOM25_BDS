<?php
namespace App\Services;

class VnPayService
{
    private $tmnCode;
    private $hashSecret;
    private $url;
    private $returnUrl;

    public function __construct()
    {
        $this->tmnCode = env('VNP_TMN_CODE');
        $this->hashSecret = env('VNP_HASH_SECRET');
        $this->url = env('VNP_URL');
        $this->returnUrl = env('VNP_RETURN_URL');
    }

    /**
     * Tạo URL thanh toán
     */
    public function createPaymentUrl($orderCode, $amount, $ip, $info)
    {
        $vnp_Url = $this->url;
        $vnp_Returnurl = $this->returnUrl;
        $vnp_TmnCode = $this->tmnCode;
        $vnp_HashSecret = $this->hashSecret;

        $vnp_TxnRef = $orderCode;
        $vnp_OrderInfo = $info;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $amount * 100; // ⚠️ VNPay yêu cầu nhân 100
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $ip;
        $vnp_CreateDate = date('YmdHis');

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $vnp_CreateDate,
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            else $hashdata .= urlencode($key) . "=" . urlencode($value);
            $i = 1;
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

        return $vnp_Url;
    }

    /**
     * Xác thực chữ ký IPN/Return
     */
    public function verifySignature($requestData)
    {
        $vnp_SecureHash = $requestData['vnp_SecureHash'];
        unset($requestData['vnp_SecureHash']);
        unset($requestData['vnp_SecureHashType']);

        ksort($requestData);
        $hashData = "";
        $i = 0;
        foreach ($requestData as $key => $value) {
            if ($i == 1) $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            else $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
            $i = 1;
        }

        $secureHash = hash_hmac('sha512', $hashData, $this->hashSecret);
        return $secureHash === $vnp_SecureHash;
    }

    public function getIpAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
        return $_SERVER['REMOTE_ADDR'];
    }
}