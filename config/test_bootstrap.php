<?php

// Bootstrap for PHPUnit tests

// Do stuff before intializing $app.

require __DIR__ . '/bootstrap.php';

$app['monolog']->debug('Test bootstrap.');

// Do stuff after intializing $app.
