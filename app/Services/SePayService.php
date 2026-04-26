<?php
//app/Services/SePayService.php
namespace App\Services;

use App\Models\GoiTin;
use Illuminate\Support\Facades\Log;
use SePay\Builders\CheckoutBuilder;
use SePay\SePayClient;
use Symfony\Component\HttpFoundation\Request;

class SePayService
{
    protected $client;

    public function __construct()
    {
        //dd(config('services.sepay'));

        $this->client = new SePayClient(
            config('services.sepay.merchant_id'),
            config('services.sepay.secret_key'),
            config('services.sepay.env', 'sandbox')
        );
    }

    public function verifyWebhook($request)
    {
        // Thử cả 2 cách lấy header
        $token = $request->header('Authorization')
            ?? $request->header('authorization')
            ?? $request->server->get('HTTP_AUTHORIZATION');

        $expected = 'Apikey ' . config('services.sepay.webhook_token');

        Log::info('Webhook Auth', [
            'received' => $token,
            'expected' => $expected,
            'match' => $token && hash_equals($expected, $token)
        ]);

        return $token && hash_equals($expected, $token);
    }

    public function createPaymentUrl($orderCode, $amount, $info, $successUrl = null, $errorUrl = null, $cancelUrl = null)
    {
        // ✅ Lấy base URL
        $returnUrl = $successUrl ?? config('services.sepay.return_url');

        // ✅ Append QUERY PARAMS thay vì path segments
        $successUrlWithParams = rtrim($returnUrl, '/') . '?status=success&order_code=' . $orderCode;
        $errorUrlWithParams = rtrim($returnUrl, '/') . '?status=error&order_code=' . $orderCode;
        $cancelUrlWithParams = rtrim($returnUrl, '/') . '?status=cancel&order_code=' . $orderCode;

        $checkoutData = CheckoutBuilder::make()
            ->currency('VND')
            ->orderInvoiceNumber($orderCode)
            ->orderAmount($amount)
            ->operation('PURCHASE')
            ->orderDescription($info)
            ->successUrl($successUrlWithParams)  // ✅ /payment/sepay-return?status=success&order_code=...
            ->errorUrl($errorUrlWithParams)      // ✅ /payment/sepay-return?status=error&order_code=...
            ->cancelUrl($cancelUrlWithParams)    // ✅ /payment/sepay-return?status=cancel&order_code=...
            ->build();

        return $this->client->checkout()->generateFormHtml($checkoutData);
    }
}
