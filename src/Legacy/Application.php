<?php

namespace Legacy;

use Silex\Application as SilexApplication;
use Symfony\Component\HttpFoundation\Response;

class Application extends SilexApplication
{

    private $settings;

    public function __construct($settings)
    {
        parent::__construct();
        $this->settings = $settings;

        // Are we runnign application in DEBUG mode?
        $this['debug'] = $settings['DEBUG'];

        $this->registerServices();
    }

    private function registerServices()
    {
        $app = $this;

        $app['todo.repository'] = $app->share(function () use ($app) {
            return new Database\Repository\Todo($app);
        });
        $app['todo.controller'] = $app->share(function () use ($app) {
            return new Controller\Todo($app, $app['todo.repository']);
        });

        $app['requestHelper'] = $app->share(function () use ($app) {
            return new Library\RequestHelper($app['request']);
        });
    }

    public function render($view, $data = array())
    {
        $this['twig']->addGlobal('requestHelper', $this['requestHelper']);

        return $this['twig']->render($view, $data);
    }

    public function renderError(\Exception $e, $code)
    {
        if ($code === 404) {
            $template = $this->render('error/404.twig', array(
                'message' => 'Could not find page "' . $this['request']->getRequestUri() . '" you were looking for.',
                'code' => 404
            ));
        } else {
            $message = ($this['debug']) ? $e->getMessage() : 'Something went wrong.';
            $template = $this->render('error/generic.twig', array(
                'message' => $message,
                'code' => 500
            ));
        }

        $this['monolog']->debug('Responding to error with: ' . $template);

        return new Response($template);
    }

    public function getSetting($key)
    {
        return (isset($this->settings[$key])) ? $this->settings[$key] : null;
    }

}
