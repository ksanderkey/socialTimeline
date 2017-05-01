<?php

namespace App\ApiWrapper;

/**
 * Class TwitterApiWrapper
 */
class TwitterApiWrapper extends ApiWrapper
{
    /**
     * @var string
     */
    private $oauth_access_token;

    /**
     * @var string
     */
    private $oauth_access_token_secret;

    /**
     * @var string
     */
    private $consumer_key;

    /**
     * @var string
     */
    private $consumer_secret;

    /**
     * @var array
     */
    protected $oauthParams;

    /**
     * TwitterApiWrapper constructor.
     * @param $apiUrl
     */
    public function __construct($apiUrl, $apiAccessData)
    {
        parent::__construct($apiUrl);
        foreach ($apiAccessData as $key => $val) {
            $this->{$key} = $val;
        }
    }

    /**
     * @param $query
     * @param string $requestMethod
     * @return $this
     */
    protected function prepareOauth($query, $requestMethod = "GET")
    {
        $oauthParams = array(
            'oauth_consumer_key' => $this->consumer_key,
            'oauth_nonce' => base64_encode(time()),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token' => $this->oauth_access_token,
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0'
        );

        $queryFields = str_replace('?', '', explode('&', $query));
        foreach ($queryFields as $keyVal) {
            $keyValSepar = explode('=', $keyVal);
            if (isset($keyValSepar[1])) {
                $oauthParams[$keyValSepar[0]] = urldecode($keyValSepar[1]);
            }
        }

        $signatureBaseString = $this->signatureBaseString($requestMethod, $oauthParams);
        $key = rawurlencode($this->consumer_secret) . '&' . rawurlencode($this->oauth_access_token_secret);
        $oauthSignature = base64_encode(hash_hmac('sha1', $signatureBaseString, $key, true));
        $oauthParams['oauth_signature'] = $oauthSignature;
        $this->oauthParams = $oauthParams;

        return $this;
    }

    /**
     * @param $query
     * @param $requestMethod
     */
    protected function prepareRequest($query, $requestMethod)
    {
        $headerString = $this->prepareOauth($query, $requestMethod)->getAuthHeader();
        $this->curlOptions[CURLOPT_HTTPHEADER] = [$headerString];
    }

    /**
     * @param $method
     * @param $params
     * @return string
     */
    private function signatureBaseString($method, $params)
    {
        $signature = array();
        ksort($params);
        foreach ($params as $key => $value) {
            $signature[] = rawurlencode($key) . '=' . rawurlencode($value);
        }

        return strtoupper($method) . "&" . rawurlencode($this->apiUrl) . '&' . rawurlencode(implode('&', $signature));
    }

    /**
     * @return string
     */
    private function getAuthHeader()
    {
        $header = 'Authorization: OAuth ';
        $values = array();
        foreach ($this->oauthParams as $key => $value) {
            if (in_array($key, array('oauth_consumer_key', 'oauth_nonce', 'oauth_signature',
                'oauth_signature_method', 'oauth_token', 'oauth_timestamp', 'oauth_version'))) {
                $values[] = $key . '="' . rawurlencode($value) . '"';
            }
        }

        $header .= implode(', ', $values);

        return $header;
    }
}