<?php

require __DIR__ . '/../config/bootstrap.php';

$authenticationMiddleware = new Web\Middleware\TokenAuthentication();

// Accepting a JSON Request Body in every route.
$jsonMiddleware = new Web\Middleware\JSONRequest();
$app->before(array($jsonMiddleware, 'before'));

//$app->post('/api/some-resource', 'some_resource.controller:create')->before(array($authenticationMiddleware, 'before'));

$app->get('/', 'todo.controller:index');
$app->run();
