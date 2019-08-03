
# DizPay PHP SDK


This repo contains the open source PHP SDK that allows you to access [DizPay](https://www.dizpay.com/) API from your PHP app.

Here are few type api in the SDK:

+ Assets API
+ Checkout API
+ Orders API
+ Rates API


# Prerequisites

+ PHP 5.6 or above


# Usage

The [sample](./sample) are good place to start. 

```php
require_once __DIR__.'/../vendor/autoload.php';
$profile = new \DizPay\DizPayProfile('YOUR_APP_ID', 'YOUR_APP_KEY');
$checkout = \DizPay\DizPayAPIFactory::checkout($profile);
```

To make [API](https://www.dizpay.com/en/docs) calls:

```php

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

```

# SDK Documents

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

$assets = \DizPay\Api\Assets($profile);
$chekout = \DizPay\Api\Checkout($profile);
$orders = \DizPay\Api\Orders($profile);
$rates = \DizPay\Api\Rates($profile);

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
