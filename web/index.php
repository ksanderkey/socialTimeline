<?php

// to serve static files
$filename = __DIR__ . preg_replace( '#(\?.*)$#', '', $_SERVER['REQUEST_URI'] );
if ( php_sapi_name() === 'cli-server' && is_file( $filename ) ) {
    return false;
}

use App\App;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

$app = new App();
$app->run();