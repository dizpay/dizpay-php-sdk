<?php
/**
 * Copyright 2019 DizPay, Inc.
 *
 */

namespace DizPay\Api;

use DizPay\Common\BaseAPI;

/**
 * Class Orders
 *
 * @method $this createWithdrawalOrder(array $payloads)
 * @method $this createInternalOrder(array $payloads)
 * @method $this createChargeOrder(array $payloads) params: ['number' => '', 'currency_code' => '', 'erc20_token' => '0/1', 'amount' => '']
 * @method $this queryOrder(array $payloads)
 * @method $this createPayoutOrder(array $payloads) According to demand, set arguments and create merchant order.
 * @method $this payOrder(array $payloads)
 * @method $this cancelOrder(array $payloads)
 *
 * @package DizPay\Api
 */
class Orders extends BaseAPI
{
    protected $prefix = 'v2/member/orders';
}
