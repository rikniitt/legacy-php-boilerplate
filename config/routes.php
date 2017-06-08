<?php

/**
 * Register all application routes and
 * middlewares to $app.
 *
 * @see: https://silex.sensiolabs.org/index.php/doc/1.3/usage.html#routing
 * @see: https://silex.sensiolabs.org/index.php/doc/1.3/middlewares.html
 */

//$authenticationMiddleware = new Legacy\Middleware\TokenAuthentication();
//$jsonMiddleware = new Legacy\Middleware\JSONRequest();
//$app->post('/api/some-resource', function () use ($app) {
//    $receivedData = $app['request']->request->all();
//    $receivedData['receivedContentType'] = $app['request']->headers->get('Content-Type'); // application/json
//    $receivedData['receivedAuthenticationToken'] = $app['request']->headers->get('X-Authentication-Token');
//    $receivedData['info'] = 'This is api endpoint requiring authenctication token on header and accepting a JSON Request Body';
//    return $app->json($receivedData, 201);
//})
//->before(array($authenticationMiddleware, 'before'))
//->before(array($jsonMiddleware, 'before'));

//$sessionMiddleware = new Legacy\Middleware\Session();
//$app->get('/login', function () use ($app) {
//    $app['request']->getSession()->set('userId', 666);
//    return sprintf('<h1>User #%d currently logged in</h1>', $app['request']->getSession()->get('userId'));
//})->before(array($sessionMiddleware, 'before'));

//$ipWhitelistMiddleware = new Legacy\Middleware\IPWhitelist();
//$app->get('/intranetz', function () {
//    return '<h1>Company wide announcementz here!</h1>';
//})->before(array($ipWhitelistMiddleware, 'before'));

//$basicAuthMiddleware = new Legacy\Middleware\HttpBasicAuthentication();
//$app->get('/admin', function () {
//    return '<h1>Administrator area!</h1>';
//})->before(array($basicAuthMiddleware, 'before'));

$app->get('/todo/delete/{id}', 'todo.controller:delete');
$app->get('/todo/edit/{id}', 'todo.controller:edit');
$app->post('/todo/update/{id}', 'todo.controller:update');
$app->get('/todo/create', 'todo.controller:create');
$app->post('/todo/save', 'todo.controller:save');
$app->get('/todo/{id}', 'todo.controller:show');
$app->get('/todo', 'todo.controller:index');
$app->get('/', 'todo.controller:index');
