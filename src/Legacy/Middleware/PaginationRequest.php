<?php

namespace Legacy\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Legacy\Application;
use Legacy\Library\Pagination;

class PaginationRequest
{

    public function before(Request $request, Application $app)
    {
        $app['pagination'] = new Pagination($request->query->all(), $app['request.helper']);

        $app['monolog']->debug('Initializing Pagination:', $app['pagination']->asArray());
    }

}
