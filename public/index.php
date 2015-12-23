<?php

require __DIR__ . '/../config/bootstrap.php';

// Accepting a JSON Request Body in every route.
$jsonMiddleware = new Legacy\Middleware\JSONRequest();
$app->before(array($jsonMiddleware, 'before'));

//$authenticationMiddleware = new Legacy\Middleware\TokenAuthentication();
//$app->post('/api/some-resource', 'some_resource.controller:create')->before(array($authenticationMiddleware, 'before'));

//$sessionMiddleware = new Legacy\Middleware\Session();
//$app->get('login', 'login_controller:login')->before(array($sessionMiddleware, 'before'));

$app->get('/', 'todo.controller:index');
$app->run();
