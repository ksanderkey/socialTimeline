<?php

namespace App;

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
    protected function getUri(){
        if(!empty($_SERVER['REQUEST_URI'])) {
            return trim($_SERVER['REQUEST_URI'], '/');
        }
    }

    /**
     * @return null
     */
    protected function getMethod(){
        return $_SERVER['REQUEST_METHOD'] ?? null;
    }

    /**
     * @param $params
     * @return string
     */
    public function postTimeline($params)
    {
        return json_encode($params);
    }
}