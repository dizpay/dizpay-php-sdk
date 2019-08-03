<?php
/**
 * Copyright 2019 DizPay, Inc.
 *
 */

namespace DizPay\Api;


use DizPay\Common\BaseAPI;

/**
 * Class Assets
 *
 * @method $this list(array $payloads) According to arguments and inquiry crypto balance record of member.
 *
 * @package DizPay\Api
 */
class Assets extends BaseAPI
{
    protected $prefix = 'v2/member/assets';
}
