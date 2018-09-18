<?php

/**
 * Create new Legacy (Silex) application
 * and configure log, db and etc.
 *
 * @see: https://silex.sensiolabs.org/doc/2.0/providers/#built-in-service-providers
 */

// Define path constans.
define('ROOT_DIR', realpath(__DIR__ . '/../'));
define('VIEW_DIR', ROOT_DIR . '/views');
define('LOG_DIR', ROOT_DIR . '/logs');
define('MODEL_DIR', ROOT_DIR . '/src/Legacy/Database/Model');

// Require composer dependencies.
$composerLoader = require ROOT_DIR . '/vendor/autoload.php';

// Load the settings from config file.
$parser = new M1\Env\Parser(file_get_contents(ROOT_DIR . '/config/config.file'));
$settings = $parser->getContent();

// Set php settings
date_default_timezone_set($settings['TIMEZONE']);


// Create silex application.
$app = new Legacy\Application($settings);

// Create logger.
$logfile = date('Ymd') . '_' . $settings['LOG_NAME'] . '.log';
$app->register(new Silex\Provider\MonologServiceProvider(), [
    'monolog.logfile' => LOG_DIR . '/' . $logfile,
    'monolog.level' => $settings['LOG_LEVEL'],
    'monolog.name' => $settings['LOG_NAME'],
    'monolog.permission' => 0664
]);

// Register doctrine dbal.
$app->register(new Silex\Provider\DoctrineServiceProvider(), [
    'dbs.options' => [
        'myWebApplication' => [
            'driver' => 'pdo_mysql',
            'host' => $settings['DB_HOST'],
            'dbname' => $settings['DB_NAME'],
            'user' => $settings['DB_USER'],
            'password' => $settings['DB_PASS'],
            'charset' => 'utf8'
        ]
        /* Example other connection
        'otherDb' => [
            'driver' => 'pdo_sqlite',
            'path' => '/some/path/to/other.db'
        ] */
    ]
]);
// Bind custom logger to Doctrine DBAL.
$app['db.config']->setSQLLogger(new Legacy\Database\Doctrine\SqlLogger($app['monolog']));
// Register doctrine orm.
$app->register(new Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider(), [
    'orm.ems.default' => 'myWebApplication',
    'orm.ems.options' => [
        'myWebApplication' => [
            'connection' => 'myWebApplication',
            'mappings' => [
                [
                    'type' => 'annotation',
                    'namespace' => 'Legacy\Database\Model',
                    'path' => MODEL_DIR,
                    'use_simple_annotation_reader' => false // using @ORM\Entity instead of plain @Entity
                ]
            ]
        ]
        /*
        'otherDb' => [
            'connection' => 'otherDb',
            'mappings' => [
                [
                    'type' => 'annotation',
                    'namespace' => 'Legacy\Database\Model',
                    'path' => MODEL_DIR,
                    'use_simple_annotation_reader' => false // using @ORM\Entity instead of plain @Entity
                ]
            ]
        ] */
    ]
]);
// Register custom datetime data type.
Doctrine\DBAL\Types\Type::addType('cdatetime', 'Legacy\Database\Doctrine\DateTimeType');

// Register twig engine.
$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => VIEW_DIR,
]);

// Register whoops debugging provider
if ($settings['DEBUG']) {
    $app->register(new WhoopsSilex\WhoopsServiceProvider());
}

// Register silex controller provider which supports identifier:method route callbacks
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
// or register custom controller provider. Supports Controller@method route callbacks
//$app->register(new Legacy\Library\Silex\ServiceControllerServiceProvider());


// Register silex session provider
$app->register(new Silex\Provider\SessionServiceProvider());

// Generic error routine.
$app->error(function (\Exception $e, $code) use ($app) {
    return $app->renderError($e, $code);
});

// Register application services.
require ROOT_DIR . '/config/services.php';

$app['monolog']->debug('Application created and configured.');
return $app;
