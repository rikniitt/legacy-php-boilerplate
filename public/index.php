<?php

require __DIR__ . '/../config/bootstrap.php';

$app->get('/', 'todo.controller:index');
$app->run();
