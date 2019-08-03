<?php
/**
 * Copyright 2019 DizPay, Inc.
 *
 */


namespace DizPay;


/**
 * Class DizPayAPIFactory
 *
 * @method static \DizPay\Api\Assets assets(DizPayProfile $profile)
 * @method static \DizPay\Api\Checkout checkout(DizPayProfile $profile)
 * @method static \DizPay\Api\Orders orders(DizPayProfile $profile)
 * @method static \DizPay\Api\Rates rates(DizPayProfile $profile)
 *
 * @package DizPay
 */
class DizPayAPIFactory
{
    public static function make($name, $profile)
    {
        $apiClass = '\\DizPay\\Api\\' . ucfirst($name);
        return new $apiClass($profile);
    }

    public static function __callStatic($name, $arguments)
    {
        return self::make($name, $arguments[0]);
    }
}
