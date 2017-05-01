<?php

namespace App;

use App\ApiWrapper\TwitterApiWrapper;

/**
 * Application
 */
class App
{
    /**
     * @var array simple routs data
     */
    protected $simpleRouts = [
        'timeline'
    ];

    /**
     * Start App
     */
    public function run()
    {
        $uri = $this->getUri();
        $method = $this->getMethod();
        $params = $_POST;

        foreach ($this->simpleRouts as $rout) {
            if ($rout == $uri) {
                if (method_exists($this, strtolower($method) . ucfirst($rout))) {
                    header('Content-type: application/json'); // return json as default
                    echo call_user_func(array($this, strtolower($method) . ucfirst($rout)), $params);
                    die;
                }
            }
        }

        // nothing to serve
        header("HTTP/1.0 404 Not Found");
        return;
    }

    /**
     * @return string
     */
    protected function getUri()
    {
        if (!empty($_SERVER['REQUEST_URI'])) {
            return trim($_SERVER['REQUEST_URI'], '/');
        }
    }

    /**
     * @return null
     */
    protected function getMethod()
    {
//        return $_SERVER['REQUEST_METHOD'] ?? null;
        return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null;
    }

    /**
     * @param $params
     * @return string
     */
    public function postTimeline($params)
    {
//        $username = $params['username'] ?? null;
        $username = isset($params['username']) ? $params['username'] : null;
        if (!$username) {
            return json_encode(['error' => 'Username is missing!']);
        }

        /**
         * Create/get access tokens - https://apps.twitter.com/
         * Current tokens only for test!
         */
        $apiAccessData = array(
            'oauth_access_token' => "210630209-Jq7x2I2meCMyZzNOHBp3jgDOgrCY1o9T8nn0MpqE",
            'oauth_access_token_secret' => "GlfaKe93XJY4QFenur09QD5hWzfxF6ry7Eq1ystt83ZEI",
            'consumer_key' => "0oEaARysYfRMAztHNfqckebdt",
            'consumer_secret' => "yypBY9MZ4b9jMGx13UlP4sEIoiHIEMjufNCZTgwbHlKHLxwDh0"
        );

        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
//        $query = '?screen_name=mathewi&count=25';
        $count = 25;
        $query = sprintf('?screen_name=%s&count=%s', $username, $count);

        $twitter = new TwitterApiWrapper($url, $apiAccessData);
        return $twitter->request($query, 'GET');
    }
}