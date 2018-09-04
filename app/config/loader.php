<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerNamespaces(array(
    'ScorpWebs\Tools' => __DIR__ . '/../../vendor/scorpwebs/fpdf-phalcon/',
    'Property\Forms' => __DIR__ . '/../forms/',
    'Property\Models' => __DIR__ . '/../models/',
    'Property\Classes' => __DIR__ . '/../classes/',
    'Property\Library' => __DIR__ . '/../library/',
    'Property\Mail' => __DIR__ . '/../library/Mail/',
    'Property\Auth' => __DIR__ . '/../library/Auth/',
    'Property\Helpers' => __DIR__ . '/../helpers/',
	//'Property\Controllers' => __DIR__ . '/../controllers/',
	//'Property\Helpers' => APP_PATH . $config->application->helpersDir,
	//'Property\Library' => APP_PATH . $config->application->libraryDir,
	//'Property\Models' => APP_PATH . $config->application->modelsDir,
));

$loader->registerDirs(
    [
       //$config->application->helpersDir,
        //$config->application->libraryDir,
        $config->application->controllersDir
        //$config->application->modelsDir,
    ]
);

$loader->register();

// Use composer autoloader to load vendor classes
require_once BASE_PATH . '/vendor/autoload.php';
