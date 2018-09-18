<?php

/**
 * Do all your application service bindings
 * to $app here.
 *
 * function() creates singleton and
 * $app->factory() returns new instance every time.
 *
 * @see: https://silex.sensiolabs.org/index.php/doc/2.0/services.html
 */

$app[Legacy\Application::class] = function () use ($app) {
    return $app;
};

/**
 * Use $app->resolve('Class\Path') to make container instantiate
 * object from implicitly bind classes.
 * @see: Legacy\Application::resolve
 */

$app['todo.repository'] = function () use ($app) {
    return new Legacy\Database\Repository\Todo($app);
};
$app['todo.controller'] = function () use ($app) {
    return new Legacy\Controller\Todo($app, $app['todo.repository']);
};

$app['request.helper'] = function () use ($app) {
    return new Legacy\Library\RequestHelper($app['current.request']);
};
$app['alerts'] = function () {
    return new Legacy\Library\Alerts\Container();
};
$app['flash.alerts'] = function () use ($app) {
    return new Legacy\Library\Alerts\FlashContainer($app['current.request']->getSession());
};
