<?php
/**
 * Copyright 2019 DizPay, Inc.
 *
 */

require_once __DIR__.'/../vendor/autoload.php';

$profile = new \DizPay\DizPayProfile('YOUR_APP_ID', 'YOUR_APP_KEY');

// same as \DizPay\DizPayAPIFactory::checkout($profile);
$checkout = new \DizPay\Api\Checkout($profile);

$invoice = $checkout->invoice(
    [
        'order_number' => uniqid('TEST_ORDER:'),
        'name' => 'DizPay Inc.', // 'Your Merchant Name, eg: DizPay Inc.',
        'description' => 'Add crypto to your account.', // 'optional, default is: Add crypto to your {{ Domain or App Name }} account.',
        'logo_url'=> 'https://cdn.shopifycloud.com/hatchful-web/assets/c3a241ae6d1e03513dfed6f5061f4a4b.png',
        'payer_info' => 'email', // one of in ['', 'email', 'mobile']
        'pricing_type' => 'fixed_price', // one of in ['fixed_price', 'no_price'],
        'currency' => 'USD', //
        'amount' => 100,
        'crypto' => 'USDC,TUSD,PAX,GUSD,USDT,ETH,BTC,LTC,DASH', // only work when pricing_type => fixed_price
        'locale' => 'auto', // language
        'theme' => 'dark', // one of in ['light', 'dark', 'standard'],
        'redirect_url' => 'https://example.com/diz-pay-result?type=success', // on success, config in routes/web.php
        'cancel_url' => 'https://example.com/diz-pay-result?type=failed', // on failures.
        'notify_url' => 'https://demo.dizpay.com/webhook',
        'extra' => 'another-params',
    ]
);

echo 'Checkout API Call Result:' . ($invoice->isSuccessful() ?  'Successful' : 'Failed') .PHP_EOL . 'Response:' . $invoice;
