<?php

namespace Legacy;

use Silex\Application as SilexApplication;
use Symfony\Component\HttpFoundation\Response;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;

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

    public function render($view, $data = [])
    {
        $this['twig']->addGlobal('requestHelper', $this['request.helper']);

        $viewData = array_merge([
            'alerts' => $this['alerts']->getAll()
        ], $data);

        return $this['twig']->render($view, $viewData);
    }

    public function renderError(\Exception $e, $code)
    {
        if ($code === 404) {
            $uri = $this['current.request']->getRequestUri();
            $msg = sprintf('Could not find page "%s" you were looking for.', $uri);
            $template = $this->render('error/404.twig', [
                'message' => $msg,
                'code' => 404
            ]);
        } else {
            $msg = ($this['debug']) ? $e->getMessage() : 'Something went wrong.';
            $template = $this->render('error/generic.twig', [
                'message' => $msg,
                'code' => 500
            ]);
        }

        $this['monolog']->debug('Responding to error with: ' . $template);

        return new Response($template);
    }

    public function getSetting($key, $default = null)
    {
        return (isset($this->settings[$key])) ? $this->settings[$key] : $default;
    }

    /**
     * Gets a parameter or an object.
     *
     * Basically same as pimple offsetGet-method.
     * Pimple implements ArrayAcces and the the array access notation ($array['key']),
     * causes the offsetGet to be called to return the bind service from container.
     *
     * This methods does same, except if it can't find the explicit binding
     * for $id, it will try to interpret $id as class path and create
     * new instance from class.
     *
     * Type hinted constructor parameters for these objects are
     * also resolved from container.
     *
     * TL;DR Adds implicit service bindings for all classes
     *       which can be resolved from container or
     *       which can be initialized without
     *       or with default arguments.
     *
     *       Example: $app->resolve('DateTime') will
     *       initialize new datetime object.
     */
    public function resolve($id)
    {
        $logger = $this['monolog'];

        try {
            return $this->offsetGet($id);
        } catch (InvalidArgumentException $e) {
            if (!class_exists($id)) {
                $logger->error(sprintf('Attempted to resolve non-existing class %s', $id));
                throw $e;
            }

            $reflection = new ReflectionClass($id);
            $constructor = $reflection->getConstructor();

            if (!$reflection->isUserDefined()
                && $reflection->isInstantiable()
                && $constructor
                && $constructor->getNumberOfRequiredParameters() === 0) {
                // Built in without params
                return new $id;
            }

            if (!$constructor && $reflection->isInstantiable()) {
                // User defined without constructor
                return new $id;
            }

            if (!$constructor) {
                // not instantiable
                $logger->error(sprintf('Attempted to resolve non instantiable class %s', $id));
                throw $e;
            }

            $initialize = [];

            foreach ($constructor->getParameters() as $param) {
                if ($param->isOptional()) {
                    // go with default value
                    try {
                        $initialize[] = $param->getDefaultValue();
                    } catch (ReflectionException $rex) {
                        throw $e;
                    }
                } elseif ($clazz = $param->getClass()) {
                    // is type hinted
                    try {
                        $initialize[] = $this->resolve($clazz->getName());
                    } catch (InvalidArgumentException $uex) {
                        $msg = sprintf(
                            'Unable to resolve type hinted constructor argument %s'
                            . ' for constructor %s',
                            $clazz->getName(),
                            $id
                        );
                        $logger->error($msg);
                        throw $e;
                    }
                } else {
                    // cant resolve
                    $logger->error(
                        sprintf(
                            'Unable to resolve all constructor arguments for class %s',
                            $id
                        )
                    );
                    throw $e;
                }
            }

            return $reflection->newInstanceArgs($initialize);
        }
    }

}
