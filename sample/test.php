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
        'order_number' => uniqid('TEST_ORDER:'), # required
        'name' => 'DizPay Inc.', // 'Your Merchant Name, eg: DizPay Inc.',
        'description' => 'Add crypto to your account.', // 'optional, default is: Add crypto to your {{ Domain or App Name }} account.',
        'logo_url'=> 'https://cdn.shopifycloud.com/hatchful-web/assets/c3a241ae6d1e03513dfed6f5061f4a4b.png',
        'payer_info' => 'email', // one of in ['', 'email', 'mobile']
        'pricing_type' => 'fixed_price', // one of in ['fixed_price', 'no_price'],
        'currency' => 'USD', // required, split by commas, valid option is USD | CNY | GBP | BTC | ETH | LTC | DASH | USDT | TUSD | GUSD | PAX | USDC
        'amount' => 100,    // required
        'rate' => 'huobi', // optional, default is huobi, one of in ['okex', 'binance', 'huobi', 'kraken']
        'crypto' => 'USDC,TUSD,PAX,GUSD,USDT,ETH,BTC,LTC,DASH', // required, split by commas, valid option is BTC | ETH | LTC | DASH | USDT | TUSD | GUSD | PAX | USDC
        'locale' => 'auto', // language, one of in ['auto', 'en', 'cn', 'ru', 'ko', 'jp']
        'success_url' => 'https://example.com/diz-pay-result?type=success', // required, on success, config in routes/web.php
        'cancel_url' => 'https://example.com/diz-pay-result?type=failed', // required, on failures.
        'notify_url' => 'https://demo.dizpay.com/webhook',
        'extra' => 'another-params',
    ]
);

echo 'Checkout API Call Result:' . ($invoice->isSuccessful() ?  'Successful' : 'Failed') .PHP_EOL . 'Response:' . $invoice;
