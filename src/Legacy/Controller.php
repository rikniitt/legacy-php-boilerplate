<?php

namespace Legacy;

use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    protected $app;

    protected $statusCode = 200;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    protected function render($view, $data = array())
    {
        $template = $this->app->render($view, $data);
        return new Response($template, $this->statusCode);
    }

    protected function url($path = '')
    {
        return $this->app['requestHelper']->url($path);
    }

}
