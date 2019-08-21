
# DizPay PHP SDK


This repo contains the open source PHP SDK that allows you to access [DizPay](https://www.dizpay.com/) API from your PHP app.

SDK apis are organized by 4 groups::

+ Assets API
+ Checkout API
+ Orders API
+ Rates API


# Prerequisites

+ PHP 5.6 or above


# Usage


DizPay SDK can integrate with multi framework, such as [Laravel](https://laravel.com/), [Lumen](https://lumen.laravel.com/) and [ThinkPHP](http://www.thinkphp.cn/), and so on.

All you need to do is 1) install the SDK deps with `composer` by use command show below:

`composer require dizpay/dizpay-php-sdk:dev-master`

2) Next you just need to setup like this:

```php
require_once __DIR__.'/../vendor/autoload.php';
$profile = new \DizPay\DizPayProfile('YOUR_APP_ID', 'YOUR_APP_KEY');
$checkout = \DizPay\DizPayAPIFactory::checkout($profile);
```

3) To make [API](https://www.dizpay.com/en/docs) calls:

```php

$invoice = $checkout->invoice(
    [
        'order_number' => uniqid('TEST_ORDER:'), # required
        'name' => 'DizPay Inc.', // 'Your Merchant Name, eg: DizPay Inc.',
        'description' => 'Add crypto to your account.', // 'optional, default is: Add crypto to your {{ Domain or App Name }} account.',
        'logo_url'=> 'https://cdn.shopifycloud.com/hatchful-web/assets/c3a241ae6d1e03513dfed6f5061f4a4b.png',
        'payer_info' => 'email', // one of in ['', 'email', 'mobile']
        'pricing_type' => 'fixed_price', // one of in ['fixed_price', 'no_price'],
        'currency' => 'USD', // required, split by commas, valid option is USD | CNY | GBP | BTC | ETH | LTC | DASH | USDT | TUSD | GUSD | PAX | USDC
        'amount' => '100',    // string, required
        'rate' => 'huobi', // optional, default is coinmarketcap, one of in ['coinmarketcap', 'okex', 'binance', 'huobi']
        'crypto' => 'USDC,TUSD,PAX,GUSD,USDT,ETH,BTC,LTC,DASH', // required, split by commas, valid option is BTC | ETH | LTC | DASH | USDT | TUSD | GUSD | PAX | USDC
        'locale' => 'auto', // language, one of in ['auto', 'en', 'cn', 'ru', 'ko', 'jp']
        'success_url' => 'https://example.com/diz-pay-result?type=success', // optional, redirect to the merchant URL after successful payment.
        'cancel_url' => 'https://example.com/diz-pay-result?type=failed', // optional, edirect to a failure URL when the charge failed to complete. The buyer cancels the order or the payment expired.
        'notify_url' => 'https://demo.dizpay.com/webhook',                // optional, Send information to the callback URL when charge has been confirmed and the associated payment is completed.
        'extra' => 'another-params',
    ]
);

echo 'Checkout API Call Result:' . ($invoice->isSuccessful() ?  'Successful' : 'Failed') .PHP_EOL . 'Response:' . $invoice;

```

The [sample](./sample) are good place to start.



# SDK Documents


There are two ways to integrate DizPay SDK: `checkout` and `api`.
* `checkout` is our built-in integration, including default UI.
* `api` is a native way, in this way you need to implement your own UI.

### Checkout

By `checkout`, you just need to call `checkout::invoice` api, which returns a pay URL address. You need to guide your user to visit it. When user finishes paying, DizPay will notify the corresponding result to you by both sync (via `success_url`) and async (via `notify_url`) notifications.
* Sync notification: does not take any arguments, you should always treat it as success whenever it reaches
* Async notification: DizPay will `POST` payStatus/amount/orderNo to the address(`notify_url`)

#### API
Using this way means you need to implement your own cashier procedure with DizPay [API Reference](https://www.dizpay.com/en/docs). The procedure is quite complicated.


There are two ways to create api instance.

+ By Factory
```php

$profile =  new \DizPay\DizPayProfile('YOUR_APP_ID', 'YOUR_APP_KEY');

$assets = \DizPay\DizPayAPIFactory::assets($profile);
$chekout = \DizPay\DizPayAPIFactory::checkout($profile);
$orders = \DizPay\DizPayAPIFactory::orders($profile);
$rates = \DizPay\DizPayAPIFactory::rates($profile);
```

+ By `new` operator
```php

$profile =  new \DizPay\DizPayProfile('YOUR_APP_ID', 'YOUR_APP_KEY');

$assets = new \DizPay\Api\Assets($profile);
$chekout = new \DizPay\Api\Checkout($profile);
$orders = new \DizPay\Api\Orders($profile);
$rates = new \DizPay\Api\Rates($profile);

``` 

API contains two property named `baseUri` and `prefix`, Each API is send as: `baseUri + prefix + endPoint`, the `endPoint` is resolved by method name.

Thanks to PHP magic method `__call`, which can convert the method name (SDK API name, which is camelcase ) to snakecase and use it as the actual request endpoint.

For example. `$order->createWithdrawalOrder(...)` will be converted to `create_withdrawal_order`.  

So, the finally request is:


```bash
curl -X POST -H 'Content-Type: application/json' -H 'Accept: application/json' -d <YOUR_FORM_DATA>  https://api.dizpay.com/v2/member/orders/create_withdrawal_order
```
Source Code: 

```php
    // Source Code at src/DizPay/Common/BaseAPI.php
    
    public function __call($name, $arguments)
    {
        $endPoint = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));

        $payloads = $this->preparedPayload($arguments ? $arguments[0] : []);

        $this->response = $this->httpClient->post($this->prefix . '/' . $endPoint,
            [
                'json' => $payloads,
            ]);

        return $this;
    }
```

# More Help

+ [API Reference](https://www.dizpay.com/en/docs)
