# DizPay SDK Samples


Simple usage you can see the source code [here](./test.php)


## Laravel example

**First, create a controller**

create a new controller named `DizPayController` in `app\Http\Controllers`


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
                'amount' => 100,    // required
                'rate' => 'huobi', // optional, default is huobi, one of in ['okex', 'binance', 'huobi', 'kraken']
                'crypto' => 'USDC,TUSD,PAX,GUSD,USDT,ETH,BTC,LTC,DASH', // required, split by commas, valid option is BTC | ETH | LTC | DASH | USDT | TUSD | GUSD | PAX | USDC
                'locale' => 'auto', // language, one of in ['auto', 'en', 'cn', 'ru', 'ko', 'jp']
                'success_url' => 'https://example.com/diz-pay-result?type=success', // required, on success, config in routes/web.php
                'cancel_url' => 'https://example.com/diz-pay-result?type=failed', // required, on failures.
                'notify_url' => $notifyUrl,
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

**Second, add route config**


```php

// dizpay in routes/api.php

Route::group(['prefix' => 'diz-pay'], function() {
    Route::post('checkout', 'DizPayController@checkout');
    Route::post('webhook', 'DizPayController@webhook');
});

```

**Last, start a http server and pay it**


use `artisan` you can start a dev http server:

`php artisan serve --port 3333`

then, you can post your form data to `/api/diz-pay/checkout` by use `curl`:

```bash

curl -H 'Accept: application/json' -H 'Content-Type: application/json' --data-binary '{"amount":10,"currency":"USD"}'  http://127.0.0.1/api/diz-pay/checkout
```

If everything is OK, you will be see a `paymentUrl` on the STDOUT. you call open it with your favorite browser.

When your payment is complete, you will be got a webhook log in  `storage/logs/***.log`.

That's all.

### Please note

> If you want to test webhook with an internal network address(e.g. `localhost`, `192.168.0.1`), you need to use `ngrok` to make your local address be able to visit from outer network.
