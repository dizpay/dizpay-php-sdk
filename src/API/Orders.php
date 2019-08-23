<?php
/**
 * Copyright 2019 DizPay, Inc.
 *
 */

namespace DizPay\Api;

use DizPay\Common\BaseAPI;

/**
 * Orders API
 *
 * @method $this createChargeOrder(array $payloads) params: ['number' => '', 'currency_code' => '', 'erc20_token' => '0/1', 'amount' => '']
 * @method $this queryOrder(array $payloads) params: ['number' => 'order number']
 * @method $this createPayoutOrder(array $payloads) According to demand, set arguments and create merchant order.
 * @method $this payOrder(array $payloads) params: ['number' => 'order number']
 * @method $this cancelOrder(array $payloads) params: ['number' => 'order number']
 *
 * @package DizPay\Api
 */
class Orders extends BaseAPI
{
    protected $prefix = 'v2/member/orders';
}
