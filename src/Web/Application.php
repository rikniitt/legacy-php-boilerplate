<?php

namespace Web;

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

    public function renderError(\Exception $e, $code)
    {
        if ($code === 404) {
            $template = $this['twig']->render('error/404.twig', array(
                'message' => 'Could not find page "' . $this['request']->getRequestUri() . '" you were looking for.',
                'code' => 404
            ));
        } else {
            $message = ($this['debug']) ? $e->getMessage() : 'Something went wrong.';
            $template = $this['twig']->render('error/generic.twig', array(
                'message' => $message,
                'code' => 500
            ));
        }

        $this['monolog']->debug('Responding to error with: ' . $template);

        return new Response($template);
    }

}
