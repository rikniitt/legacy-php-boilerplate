<?php

/**
 * Register all application routes and
 * middlewares to $app.
 *
 * @see: https://silex.sensiolabs.org/index.php/doc/2.0/usage.html#routing
 * @see: https://silex.sensiolabs.org/index.php/doc/2.0/middlewares.html
 */

$currentRequest = New Legacy\Middleware\BindCurrentRequestToApplication();
$app->before([$currentRequest, 'before'], Legacy\Application::EARLY_EVENT);

//$authenticationMiddleware = new Legacy\Middleware\TokenAuthentication();
//$jsonMiddleware = new Legacy\Middleware\JSONRequest();
//$app->post('/api/some-resource', function () use ($app) {
//    $receivedData = $app['current.request']->request->all();
//    $receivedData['receivedContentType'] = $app['current.request']->headers->get('Content-Type'); // application/json
//    $receivedData['receivedAuthenticationToken'] = $app['current.request']->headers->get('X-Authentication-Token');
//    $receivedData['info'] = 'This is api endpoint requiring authenctication token on header and accepting a JSON Request Body';
//    return $app->json($receivedData, 201);
//})
//->before([$authenticationMiddleware, 'before'])
//->before([$jsonMiddleware, 'before']);

//$sessionMiddleware = new Legacy\Middleware\Session();
//$app->get('/login', function () use ($app) {
//    $app['current.request']->getSession()->set('userId', 666);
//    return sprintf('<h1>User #%d currently logged in</h1>', $app['current.request']->getSession()->get('userId'));
//})->before([$sessionMiddleware, 'before']);

//$ipWhitelistMiddleware = new Legacy\Middleware\IPWhitelist();
//$app->get('/intranetz', function () {
//    return '<h1>Company wide announcementz here!</h1>';
//})->before([$ipWhitelistMiddleware, 'before']);

//$basicAuthMiddleware = new Legacy\Middleware\HttpBasicAuthentication();
//$app->get('/admin', function () {
//    return '<h1>Administrator area!</h1>';
//})->before([$basicAuthMiddleware, 'before']);

$app->get('/todo/delete/{id}', 'todo.controller:delete');
$app->get('/todo/edit/{id}', 'todo.controller:edit');
$app->post('/todo/update/{id}', 'todo.controller:update');
$app->get('/todo/create', 'todo.controller:create');
$app->post('/todo/save', 'todo.controller:save');
$app->get('/todo/{todo}', 'todo.controller:show')
    ->convert('todo', 'todo.repository:convert');
$app->get('/todo', 'todo.controller:index')
    ->before([new Legacy\Middleware\PaginationRequest(), 'before']);
$app->get('/', function() {
    return new Symfony\Component\HttpFoundation\RedirectResponse('/todo');
});
