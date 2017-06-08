<?php

/**
 * Do all your application service bindings
 * to $app here.
 *
 * @see: https://silex.sensiolabs.org/index.php/doc/1.3/services.html
 */

$app['todo.repository'] = $app->share(function () use ($app) {
    return new Legacy\Database\Repository\Todo($app);
});
$app['todo.controller'] = $app->share(function () use ($app) {
    return new Legacy\Controller\Todo($app, $app['todo.repository']);
});

$app['requestHelper'] = $app->share(function () use ($app) {
    return new Legacy\Library\RequestHelper($app['request']);
});
