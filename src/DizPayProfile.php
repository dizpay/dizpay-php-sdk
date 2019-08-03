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
}
