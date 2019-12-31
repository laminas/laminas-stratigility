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

        $r = new ReflectionFunction($callable);
        return $r->getNumberOfRequiredParameters();
    }
}
