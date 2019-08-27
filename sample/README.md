# DizPay PHP SDK Example

The example shown here is only applicable for the Laravel PHP framework. However, you can apply the same concepts for any other framework of your choice.

### Table of Contents

+ [Step 1: Create the DizPay Controller](#step-1-create-the-dizpay-controller)
+ [Step 2: Add the `Route` config](#step-2-add-the-route-config)
+ [Step 3: Start an HTTP Server](#step-3-start-an-http-server)
+ [Step 4: Payment](#step-4-payment)
+ [Webhook Logs](#webhook-logs)
+ [Reference](#reference)

### Step 1: Create the DizPay Controller

Create a controller named `DizPayController` inside `App\Http\Controllers`


```php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use DizPay\Api\Checkout;
use DizPay\DizPayProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DizPayController extends BaseController
{

    /**
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkout(Request $request)
    {
        $rules = [
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|between:1,10,in:USD,CNY'
        ];

        $this->validate($request, $rules);

        // replace with your app_id && app_key
        $checkout = new Checkout(new DizPayProfile(YOUR_APP_ID, YOU_APP_KEY));

        // replace with your `ngrok http <port>` result.
        $notifyUrl = url('/api/diz-pay/webhook');

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
                'notify_url' => $notifyUrl, // optional, Send information to the callback URL when charge has been confirmed and the associated payment is completed.
                'extra' => 'another-params',
            ]
        );

        if ($invoice->isSuccessful()) {
            return $this->respondCreated($invoice->toArray());
        }

        return $this->respondWithError($invoice->toArray());

    }

    /**
     *
     * DizPay pay-result webhook.
     *
     * @see checkout
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function webhook(Request $request)
    {
        // TODO: validate forms.

        $resp = $this->respond('');

        // process logic
        Log::debug('notify-result', [
            'req' => $request->all(),
            'headers' => $request->headers->all(),
            'resp' => ''
        ]);

        return $resp;
    }
}

```

### Step 2: Add the `Route` config

Create the following config in `Routes\api.php`.

```php

Route::group(['prefix' => 'diz-pay'], function() {
    Route::post('checkout', 'DizPayController@checkout');
    Route::post('webhook', 'DizPayController@webhook');
});

```

### Step 3: Start an HTTP Server

Use `artisan` to start an HTTP server:

`php artisan serve --port 3333`

### Step 4: Payment

Use the `curl` keyword to POST any form data to the URL `/api/diz-pay/checkout`

```bash

curl -H 'Accept: application/json' -H 'Content-Type: application/json' --data-binary '{"amount":10,"currency":"USD"}'  http://127.0.0.1/api/diz-pay/checkout
```

On successful response, `paymentUrl` is displayed on the STDOUT.

### Webhook Logs

Once the payment is successfully complete, a webhook log is written in `storage/logs/***.log`.

> If you want to test the webhook in an internal network address, such as the `localhost` or `192.168.0.1`, use `ngrok` to make your local address available over the internet.


### Reference

For the full source code, check out [this](./test.php) URL.
