<?php
//app/Services/SePayService.php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use SePay\Builders\CheckoutBuilder;
use SePay\SePayClient;

class SePayService
{
    protected $client;

    public function __construct()
    {
        $this->client = new SePayClient(
            config('services.sepay.merchant_id'),
            config('services.sepay.secret_key'),
            config('services.sepay.env', 'sandbox')
        );
    }

    public function createPaymentUrl($orderCode, $amount, $info, $returnUrl = null)
    {   
        $separator = str_contains($returnUrl, '?') ? '&' : '?'; 
        $checkoutData = CheckoutBuilder::make()
            ->currency('VND')
            ->orderInvoiceNumber($orderCode)
            ->orderAmount($amount)
            ->operation('PURCHASE')
            ->orderDescription($info)
            ->successUrl($returnUrl . $separator . 'status=success')
            ->errorUrl($returnUrl . $separator . 'status=error')
            ->cancelUrl($returnUrl . $separator . 'status=cancel')
            ->build();

        // Trả về URL redirect hoặc HTML form tùy nhu cầu
        return $this->client->checkout()->generateFormHtml($checkoutData);
    }

    public function verifyWebhook($request)
    {
        $token = $request->header('Authorization');
        $expected = 'Apikey ' . config('services.sepay.webhook_token');

        if (!$token || !hash_equals($expected, $token)) {
            Log::error('SePay Webhook: Invalid token', ['received' => $token]);
            return false;
        }

        return true;
    }
}
