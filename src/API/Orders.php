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
 * @method $this createChargeOrder(array $payloads) params: ['number' => '订单号,全局唯一', 'currency_code' => '数字货币', 'erc20_token' => '0/1', 'amount' => '数量']
 * @method $this queryOrder(array $payloads)
 * @method $this createPayoutOrder(array $payloads) According to demand, set arguments and create merchant order.
 * @method $this payOrder(array $payloads)
 * @method $this cancelOrder(array $payloads)
 *
 *
 *
 * @package DizPay\Api
 */
class Orders extends BaseAPI
{
    protected $prefix = 'v2/member/orders';
}
