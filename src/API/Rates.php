<?php
/**
 * Copyright 2019 DizPay, Inc.
 *
 */

namespace DizPay\Api;


use DizPay\Common\BaseAPI;

/**
 * Class Rates
 *
 * @method $this cryptocurrency(array $payloads = []) params: ['currency_list' => 'String Optional Currency code(Separated by commas)']
 * @method $this currency(array $payloads = []) params: ['currency_list' => 'String Optional Currency code(Separated by commas)']
 * @package DizPay\Api
 */
class Rates extends BaseAPI
{
    protected $prefix = 'v2/member/rates';
}
