<?php
/**
 * Copyright 2019 DizPay, Inc.
 *
 */

namespace DizPay\Api;


use DizPay\Common\BaseAPI;

/**
 * Class Checkout
 *
 * @method $this invoice(array $payloads)
 *
 * @package DizPay\Api
 */
class Checkout extends BaseAPI
{
    protected $baseUri = 'https://checkout.dizpay.com/';

    protected $prefix = 'v1';
}
