<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Stratigility;

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
}
