<?php
/**
 * Copyright 2019 DizPay, Inc.
 *
 */

require_once __DIR__.'/../vendor/autoload.php';

$profile = new \DizPay\DizPayProfile('YOUR_APP_ID', 'YOUR_APP_KEY');

// to check the callback payloads
$r = $profile->verifySignature(
    [
        'number' => '8eb9bc53-75ea-4142-8a91-af89d2246379',
        'currency_code' => 'USDT',
        'amount' => '100.00000000',
        'paid_amount' => '100.00000000',
        'extra' => '',
        'erc20_token' => 0,
        'status' => 2,
        'signature' => 'e27573e4915b052ebb0db96611ac9f4f'
    ]
);

echo 'Payload check result:' . var_export($r, true) . PHP_EOL;

// same as \DizPay\DizPayAPIFactory::checkout($profile);
$checkout = new \DizPay\Api\Checkout($profile);

$invoice = $checkout->invoice(
    [
        'order_number' => uniqid('TEST_ORDER:'), # required
        'name' => 'DizPay Inc.', // 'Your Merchant Name, eg: DizPay Inc.',
        'description' => 'Add crypto to your account.', // 'optional, default value is "Add crypto to your {{ Domain or App Name }} account.""',
        'logo_url'=> 'https://cdn.shopifycloud.com/hatchful-web/assets/c3a241ae6d1e03513dfed6f5061f4a4b.png',
        'payer_info' => 'email', // one of the values in ['', 'email', 'mobile']
        'pricing_type' => 'fixed_price', // one of the values in ['fixed_price', 'no_price'],
        'currency' => 'USD', // required, separate the values by commas, the valid options are ['USD', 'CNY', 'GBP', 'BTC', 'ETH', 'LTC', 'DASH', 'USDT', 'TUSD', 'GUSD', 'PAX', or 'USDC']
        'amount' => '100',    // string, required
        'rate' => 'huobi', // optional, default value is "coinmarketcap", valid values are one of the values in ['coinmarketcap', 'okex', 'binance', 'huobi']
        'crypto' => 'USDC,TUSD,PAX,GUSD,USDT,ETH,BTC,LTC,DASH', // required, separate the values by commas, the valid options are ['BTC', 'ETH', 'LTC', 'DASH', 'USDT', 'TUSD', 'GUSD', 'PAX', 'USDC']
        'locale' => 'auto', // language, one of the values in ['auto', 'en', 'cn', 'ru', 'ko', 'jp']
        'success_url' => 'https://example.com/diz-pay-result?type=success', // optional, URL to redirect the merchant after successful payment.
        'cancel_url' => 'https://example.com/diz-pay-result?type=failed', // optional, URL to redirect when the payment failed to complete. The buyer cancels the order or the payment has expired.
        'notify_url' => 'https://demo.dizpay.com/webhook', // optional, Send information to the callback URL when the charge has been confirmed and the associated payment is complete.
        'extra' => 'another-params',
    ]
);

echo 'Checkout API Call Result:' . ($invoice->isSuccessful() ?  'Successful' : 'Failed') .PHP_EOL . 'Response:' . $invoice;
