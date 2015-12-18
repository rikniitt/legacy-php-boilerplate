<?php

// Bootstrap for PHPUnit tests

// Do stuff before initializing $app.

require __DIR__ . '/bootstrap.php';

$app['monolog']->debug('Test bootstrap.');

// Do stuff after initializing $app.
