<?php
defined('BASE_PATH')    || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH')     || define('APP_PATH', BASE_PATH . '/app');
defined('URL')          || define('URL', 'http://209.97.168.158');
defined('PUBLIC_PATH')  || define('PUBLIC_PATH', URL);
defined('BASE_URL')     || define('BASE_URL', '/');
defined('MOVE_PHOTO')   || define('MOVE_PHOTO', BASE_PATH .'/public/img');

use Phalcon\Logger;

return new \Phalcon\Config([
    'database' => [
        'adapter'     => 'Mysql',
        'host'        => 'localhost',
        'username'    => 'property',
        'password'    => 'hSwDAh#8[%',
        'dbname'      => 'property',
        'charset'     => 'utf8',
    ],
    'application' => [
        'appDir'         => APP_PATH . '/',
        'controllersDir' => APP_PATH . '/controllers/',
        'migrationsDir'  => APP_PATH . '/migrations/',
        'viewsDir'       => APP_PATH . '/views/',
        'pluginsDir'     => APP_PATH . '/plugins/',
        'helpersDir'     => APP_PATH . '/helpers/',
        'libraryDir'     => APP_PATH . '/library/',
        'cacheDir'       => BASE_PATH . '/cache/',
        'vendorDir'      => BASE_PATH . '/vendor/',
        'baseUri'        => preg_replace('/public([\/\\\\])index.php$/', '', $_SERVER["PHP_SELF"]),
        'publicUrl'      => URL,
    ],
    'mail' => [
        'fromName' => 'No Reply',
        'fromEmail' => 'no-reply@allproperty.com',
        'smtp' => [
            'server' => 'smtp.gmail.com',
            'port' => 587,
            'security' => 'tls',
            'username' => 'xyclone.spam@gmail.com',
            'password' => 'cn.j4s0n'
        ]
    ],    
    'logger' => [
        'path'     => BASE_PATH . '/logs/',
        'format'   => '%date% [%type%] %message%',
        'date'     => 'D j H:i:s',
        'logLevel' => Logger::DEBUG,
        'filename' => 'application.log',
    ],
    // Set to false to disable sending emails (for use in test environment)
    'useMail' => true    
]);
