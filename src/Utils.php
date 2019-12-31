<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Stratigility;

use Exception;
use Psr\Http\Message\ResponseInterface;
use ReflectionFunction;
use ReflectionMethod;

/**
 * Utility methods
 */
abstract class Utils
{
    /**
     * Get the arity of a handler
     *
     * @param string|callable|object $callable
     * @return int
     */
    public static function getArity($callable)
    {
        if (is_object($callable)) {
            foreach (['__invoke', 'handle'] as $method) {
                if (! method_exists($callable, $method)) {
                    continue;
                }

                $r = new ReflectionMethod($callable, $method);
                return $r->getNumberOfRequiredParameters();
            }
            return 0;
        }

        if (! is_callable($callable)) {
            return 0;
        }

        // Handle static methods passed in Class::method format by re-casting
        // as an array callable.
        if (is_string($callable)
            && preg_match('/^(?P<class>[^:]+)::(?P<method>.*)$/', $callable, $matches)
        ) {
            $callable = [$matches['class'], $matches['method']];
        }

        if (is_array($callable)) {
            list($class, $method) = $callable;
            $r = new ReflectionMethod($class, $method);
            return $r->getNumberOfRequiredParameters();
        }

        $r = new ReflectionFunction($callable);
        return $r->getNumberOfRequiredParameters();
    }

    /**
     * Determine status code from an error and/or response.
     *
     * If the error is an exception with a code between 400 and 599, returns
     * the exception code.
     *
     * Otherwise, retrieves the code from the response; if not present, or
     * less than 400 or greater than 599, returns 500; otherwise, returns it.
     *
     * @param mixed $error
     * @param ResponseInterface $response
     * @return int
     */
    public static function getStatusCode($error, ResponseInterface $response)
    {
        if ($error instanceof Exception
            && ($error->getCode() >= 400 && $error->getCode() < 600)
        ) {
            return $error->getCode();
        }

        $status = $response->getStatusCode();
        if (! $status || $status < 400 || $status >= 600) {
            $status = 500;
        }
        return $status;
    }
}
