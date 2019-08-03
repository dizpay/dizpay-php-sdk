<?php
/**
 * Copyright 2019 DizPay, Inc.
 *
 */


namespace DizPay\Common;

use DizPay\DizPayProfile;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

/**
 * See https://www.dizpay.com/en/docs
 *
 * Class BaseAPI
 *
 * @package DizPay\Common
 */
abstract class BaseAPI
{
    protected $baseUri = 'https://api.dizpay.com/';

    protected $prefix = '';

    protected $httpClient;

    protected $profile;

    /**
     * @var ResponseInterface
     */
    protected $response;


    public function __construct(DizPayProfile $profile)
    {

        $this->profile = $profile;

        $options = $this->getDefaultOptions();
        $options['base_uri'] = $this->baseUri;

        $this->httpClient = new Client($options);

    }

    /**
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'http_errors' => false,
            'verify' => false,
        ];
    }

    /**
     * @param $params
     *
     * @return array
     */
    protected function preparedPayload($params)
    {
        if (empty($params)) {
            return $params;
        }
        $payloads = array_merge($params, $this->profile->toArray());
        ksort($payloads);
        $plainText = urldecode(http_build_query($payloads));
        unset($payloads['app_key']);
        $payloads['signature'] = md5($plainText);

       // echo 'RawStr:' . $plainText. PHP_EOL . 'Actual:' . $payloads['signature'].PHP_EOL.PHP_EOL;

        return $payloads;
    }

    /**
     * @param $name
     *
     * @param $arguments
     *
     * @return static
     */
    public function __call($name, $arguments)
    {
        $endPoint = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));

        $payloads = $this->preparedPayload($arguments ? $arguments[0] : []);

        $this->response = $this->httpClient->post($this->prefix . '/' . $endPoint,
            [
                'json' => $payloads,
            ]);

        return $this;
    }


    public function __toString()
    {
        return $this->response ? (string)$this->response->getBody() : '';
    }

    public function isSuccessful()
    {
        $code = $this->response->getStatusCode();

        return $code >= 200 && $code < 400;
    }

    protected function jsonDecode($asArray = false)
    {
        if (!$this->response) {
            throw new \LogicException('No response could be decode');
        }

        return json_decode($this->response->getBody(), $asArray);
    }

    public function toArray()
    {
        return $this->jsonDecode(true);
    }

    public function toObject()
    {
        return $this->jsonDecode(false);
    }
}
