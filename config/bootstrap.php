<?php

/** 
 * ini_set('display_errors', 1);
 * ini_set('display_startup_errors', 1);
 * error_reporting(E_ALL);
 */


// Define path constans.
define('ROOT_DIR', realpath(__DIR__ . '/../'));
define('VIEW_DIR', ROOT_DIR . '/views');
define('LOG_DIR', ROOT_DIR . '/logs');

// Requre composer dependencies.
require ROOT_DIR . '/vendor/autoload.php';

// Load the settings from config file. (Throws if file not exists.)
$loader = new josegonzalez\Dotenv\Loader(ROOT_DIR . '/config/config.file');
$settings = $loader->parse()->toArray();

// Set php settings
date_default_timezone_set($settings['TIMEZONE']);


// Create silex application.
$app = new Web\Application($settings);

// Create logger.
$logfile = date('Ymd') . '_' . $settings['LOG_NAME'] . '.log';
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => LOG_DIR . '/' . $logfile,
    'monolog.level' => $settings['LOG_LEVEL'],
    'monolog.name' => $settings['LOG_NAME']
));

// Register doctrine dbal.
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'host' => $settings['DB_HOST'],
        'dbname' => $settings['DB_NAME'],
        'user' => $settings['DB_USER'],
        'password' => $settings['DB_PASS'],
        'charset' => 'utf8'
    )
));

// Register twig engine.
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => VIEW_DIR,
));

// Generic error routine.
$app->error(function (\Exception $e, $code) use ($app) {
    return $app->renderError($e, $code);
});


$app['monolog']->debug('Application created and configured.');
