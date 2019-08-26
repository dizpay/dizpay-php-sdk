
# DizPay PHP SDK


The open source PHP SDK that allows you to access the [DizPay](https://www.dizpay.com/) API from your PHP app.

# Table of Contents

+ [Introduction](#Introduction)
+ [Prerequisites](#Prerequisites)
+ [Installation](#Installation)
+ [Usage](#Usage)
  + [Checkout method](#Checkout)
  + [API method](#API)
  + [Sample code](#Sample-code)
  + [Response codes](#Response-codes)
+ [Additional Reference](#Reference)

# Introduction

DizPay APIs let you integrate BTC, ETH, LTC, DASH, USDT, GUSD, PAX, TUSD and USDC payments into your online application. Using the DizPay PHP SDK, you can integrate DizPay into multiple PHP frameworks such as [Laravel](https://laravel.com/), [Lumen](https://lumen.laravel.com/) and [ThinkPHP](http://www.thinkphp.cn/).

Before you start using DizPay, make sure that you obtain your **App ID** and your **App Key** from [DizPay](https://www.dizpay.com) by signing up to the website.

The DizPay SDK APIs are grouped into:
+ Assets API
+ Checkout API
+ Orders API
+ Rates API

You can use these to send requests and get responses from DizPay. Refer to the [API Reference](https://www.dizpay.com/en/docs) manual for details on the request and response details for each of them.

# Prerequisites

PHP 5.6 or above

# Installation

Install using `composer`:

`composer require dizpay/dizpay-php-sdk:dev-master`

# Usage

Use the following lines of code in your PHP file to set up DizPay:

```php
require_once __DIR__.'/../vendor/autoload.php';
$profile = new \DizPay\DizPayProfile('YOUR_APP_ID', 'YOUR_APP_KEY');
```

Once you complete the setup, you can use the `profile` variable created above to send requests to the DizPay APIs.

There are two ways to use the DizPay SDK: `checkout` and `API`.

* `checkout` is the default integration method, where you can use the DizPay UI.

* `API` is the native method, where you need to implement your own UI.

### Checkout

To use `checkout`, call the `checkout::invoice` method. It returns a pay URL address. Simply add the provided URL to your webpage where it is accessible for all your users.

When the user finishes the payment, DizPay notifies you with the corresponding result via both the sync (via `success_url`) and async (via `notify_url`) notifications.
* Sync notification: It does not take any arguments, you should always treat it as success when it reaches.
* Async notification: DizPay sends you a `POST` notification payStatus/amount/orderNo to the address set in the `notify_url` key while creating the API.

### API
Using this method, you need to implement your own cashier procedure with DizPay [API Reference](https://www.dizpay.com/en/docs).

There are two ways to create an API instance.

+ By Factory
```php

$profile =  new \DizPay\DizPayProfile('YOUR_APP_ID', 'YOUR_APP_KEY');

$assets = \DizPay\DizPayAPIFactory::assets($profile);
$checkout = \DizPay\DizPayAPIFactory::checkout($profile);
$orders = \DizPay\DizPayAPIFactory::orders($profile);
$rates = \DizPay\DizPayAPIFactory::rates($profile);
```

+ By `new` operator
```php

$profile =  new \DizPay\DizPayProfile('YOUR_APP_ID', 'YOUR_APP_KEY');

$assets = new \DizPay\Api\Assets($profile);
$checkout = new \DizPay\Api\Checkout($profile);
$orders = new \DizPay\Api\Orders($profile);
$rates = new \DizPay\Api\Rates($profile);

```

An API contains two properties:
+ `baseUri`
+ `prefix`

Each API call is then sent to DizPay as: `baseUri + prefix + endPoint`, the `endPoint` is resolved by the method name.

The PHP magic method `__call` then converts the method name (SDK API name, which is camelcase ) to snakecase and uses it as the actual request endpoint.

For example. `$order->createChargeOrder(...)` is converted to `create_charge_order`.

So, the final request is sent as:

```bash
curl -X POST -H 'Content-Type: application/json' -H 'Accept: application/json' -d <YOUR_FORM_DATA>  https://api.dizpay.com/v2/member/orders/create_charge_order
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

### Sample code

The following lines of code show you how to get a sample invoice using the `profile` variable created during setup:

```php

$checkout = \DizPay\DizPayAPIFactory::checkout($profile);
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

To get you started, you can check out other [samples](./sample) API calls.

### Response codes

Once you send requests, the DizPay APIs send you a code in the response. The most commonly returned response codes are:

| Responce Code | Error                 | Description                                |
|---------------|-----------------------|--------------------------------------------|
| 200           | OK                    | Request succeeded                          |
| 400           | Bad                   | Request request failed                     |
| 401           | Unauthorized          | App ID is invalid or blocked               |
| 403           | Forbidden             | Signing failure                            |
| 500           | Internal Server Error | Internal server error occurred             |


DizPay API returns the following error codes in response if the response code is `400`:

| Error Code | Description                                        |
|------------|----------------------------------------------------|
| 1000       | Field Syntax Error (e.g. Required, Range, Type)    |
| 1001       | Something does not exist                           |
| 1002       | Something does not match                           |
| 1003       | Something is invalid                               |
| 1004       | Failedto send sms                                  |
| 1005       | Request too frequently                             |
| 1006       | Something is expired                               |
| 1008       | Balance is not enough                              |
| 1010       | Failed to send email                               |
| 1051       | Wallet Server went wrong                           |

# Reference

+ [API Reference](https://www.dizpay.com/en/docs)
