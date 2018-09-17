<?php

/**
 * Do all your application service bindings
 * to $app here.
 *
 * Service defined with;
 *  - function() { ... } will return new instance every time
 *  - $app->share(function() {...}) creates singleton
 *
 * @see: https://silex.sensiolabs.org/index.php/doc/1.3/services.html
 */

 $app['Legacy\Application'] = $app->share(function () use ($app) {
    return $app;
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
