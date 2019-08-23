<?php
/**
 * Copyright 2019 DizPay, Inc.
 *
 */

namespace DizPay;

class DizPayProfile
{
    protected $appId;

    protected $appKey;


    /**
     * DizPaySignature constructor.
     *
     * @param $appId
     * @param $appKey
     */
    public function __construct($appId, $appKey)
    {
        $this->appId = $appId;
        $this->appKey = $appKey;
    }

    /**
     * @return mixed
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param mixed $appId
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    /**
     * @return mixed
     */
    public function getAppKey()
    {
        return $this->appKey;
    }

    /**
     * @param mixed $appKey
     */
    public function setAppKey($appKey)
    {
        $this->appKey = $appKey;
    }

    public function toArray()
    {
        return [
            'app_id' => $this->appId,
            'app_key' => $this->appKey,
        ];
    }

    /**
     *
     * @param array $payloads
     *
     * @return bool
     */
    public function verifySignature(array $payloads)
    {
        if (empty($payloads['signature'])) {
            return false;
        }
        $signature = $payloads['signature'];
        unset($payloads['signature']);
        $payloads = array_merge($payloads, $this->toArray());
        ksort($payloads);
        $plainText = urldecode(http_build_query($payloads));
        return $signature === md5($plainText);
    }
}
