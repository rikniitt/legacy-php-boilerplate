<?php

/**
 * Do all your application service bindings
 * to $app here.
 *
 * @see: https://silex.sensiolabs.org/index.php/doc/2.0/services.html
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
$app['alerts'] = $app->share(function () {
    return new Legacy\Library\Alerts\Container();
});
$app['flash.alerts'] = $app->share(function () use ($app) {
    return new Legacy\Library\Alerts\FlashContainer($app['request']->getSession());
});