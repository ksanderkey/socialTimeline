<?php

namespace App\ApiWrapper;

/**
 * Class ApiWrapper
 */
abstract class ApiWrapper
{
    /**
     * @var string
     */
    protected $apiUrl;

    /**
     * @var array
     */
    protected $curlOptions = [
        CURLOPT_HEADER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
    ];

    /**
     * ApiWrapper constructor.
     * @param string $apiUrl
     */
    public function __construct($apiUrl)
    {
        if (!function_exists('curl_version')) {
            throw new RuntimeException('cURL extension is not loaded');
        }

        $this->apiUrl = $apiUrl;
    }

    /**
     * @param string $query
     * @param string $requestMethod
     * @param array $curlOptions
     * @return bool|mixed
     */
    public function request($query, $requestMethod = "GET", $curlOptions = array())
    {
        $this->prepareRequest($query, $requestMethod);

        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $this->apiUrl . $query,
        );

        $options = array_replace($this->curlOptions, $options, $curlOptions);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        if (200 !== curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
//            pass errors from api
//            $content = false;
        }

        curl_close($ch);

        return $content;
    }

    /**
     * @param $query
     * @param $requestMethod
     * @return mixed
     */
    abstract protected function prepareRequest($query, $requestMethod);
}