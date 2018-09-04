<?php

use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Mvc\Router;
use Property\Mail\Mail;
use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Logger\Formatter\Line as FormatterLine;
use Property\Auth\Auth;

use Property\Helpers\Helpers;
use Property\Helpers\Tag;
use Property\Library\Menu;
use Property\Library\ScrollNews;

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    $config = include APP_PATH . '/config/config.php';
    if (is_readable(APP_PATH . '/config/config.dev.php')) {
        $override = include APP_PATH . '/config/config.dev.php';
        $config->merge($override);
    }
    return $config;
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () {
    $config = $this->getConfig();

    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

/**
 * Setting up the view component
 */
$di->setShared('view', function () {
    $config = $this->getConfig();

    $view = new View();
    $view->setDI($this);
    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines([
        '.volt' => function ($view) {
            $config = $this->getConfig();

            $volt = new VoltEngine($view, $this);

            $volt->setOptions([
                'compiledPath' => $config->application->cacheDir,
                'compiledSeparator' => '_',
                'compileAlways' => true
            ]);

            $compiler = $volt->getCompiler();
            $compiler->addFunction('is_a', 'is_a');
            $compiler->addFunction('str_split', 'str_split');
            $compiler->addFunction('str_replace', 'str_replace');
            $compiler->addFunction('strpos', 'strpos');
            $compiler->addFunction('substr', 'substr');
            $compiler->addFunction('isset', 'isset');
            $compiler->addFunction('in_array', 'in_array');
            $compiler->addFunction('is_array', 'is_array');
            $compiler->addFunction('array_diff', 'array_diff');
            $compiler->addFunction('array_diff_assoc', 'array_diff_assoc');
            $compiler->addFunction('implode', 'implode');
            $compiler->addFunction('explode', 'explode');
            $compiler->addFunction('strtotime', 'strtotime');
            $compiler->addFunction('count', 'count');
            $compiler->addFunction('array_search', 'array_search');
            $compiler->addFunction('base64enc', 'base64_encode');
            $compiler->addFunction('base64dec', 'base64_decode');
            $compiler->addFunction('preg_replace', 'preg_replace');
            $compiler->addFunction('ceil','ceil');
            $compiler->addFunction('str_replace','str_replace');
            $compiler->addFunction('json_encode','json_encode');
            $compiler->addFunction(
                'convert',
                function ($resolvedArgs, $exprArgs) 
                {
                    return 'Helper::notify(' . $resolvedArgs . ')';
                }
            );

            return $volt;
        },
        '.phtml' => PhpEngine::class

    ]);

    return $view;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    $connection = new $class($params);

    return $connection;
});


/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Register the session flash service with the Twitter Bootstrap classes
 */
$di->set('flash', function () {
    return new FlashSession([
        'error'   => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice'  => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ]);
});


/**
 * Custom authentication component
 */
$di->set('auth', function () {
    return new Auth();
});


/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();
    return $session;
});

/**
 * Register the class helpers
 */
$di->set('Helpers', function () {
    return new Helpers();
});

/**
 * Register the class helpers
 */
$di->set('Tag', function () {
    return new Tag();
});

/**
 * Mail service uses AmazonSES
 */
$di->set('mail', function () {
    return new Mail();
});

/**
 * Register the class menu
 */
$di->set('Menu', function () {
    return new Menu();
});

/**
 * Register the class scrollnews
 */
$di->set('ScrollNews', function () {
    return new ScrollNews();
});

/**
 *  Route phalcon
 */
$di->set('router', function () {
    $router = new Router(false);
    include APP_PATH . "/config/router.php";
    return $router;
});


/**
 * Logger service
 */
$di->set('logger', function ($filename = null, $format = null) {
    $config = $this->getConfig();

    $format   = $format ?: $config->get('logger')->format;
    $filename = trim($filename ?: $config->get('logger')->filename, '\\/');
    $path     = rtrim($config->get('logger')->path, '\\/') . DIRECTORY_SEPARATOR;

    $formatter = new FormatterLine($format, $config->get('logger')->date);
    $logger    = new FileLogger($path . $filename);

    $logger->setFormatter($formatter);
    $logger->setLogLevel($config->get('logger')->logLevel);

    return $logger;
});