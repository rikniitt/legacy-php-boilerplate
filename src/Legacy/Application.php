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
    }

    public function render($view, $data = array())
    {
        $this['twig']->addGlobal('requestHelper', $this['request.helper']);

        $viewData = array_merge(array(
            'alerts' => $this['alerts']->getAll()
        ), $data);

        return $this['twig']->render($view, $viewData);
    }

    public function renderError(\Exception $e, $code)
    {
        if ($code === 404) {
            $uri = $this['request']->getRequestUri();
            $msg = sprintf('Could not find page "%s" you were looking for.', $uri);
            $template = $this->render('error/404.twig', array(
                'message' => $msg,
                'code' => 404
            ));
        } else {
            $msg = ($this['debug']) ? $e->getMessage() : 'Something went wrong.';
            $template = $this->render('error/generic.twig', array(
                'message' => $msg,
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
