<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Stratigility;

use Webimpress\HttpMiddlewareCompatibility\MiddlewareInterface;

/**
 * Convenience function for creating path-segregated middleware.
 *
 * Usage:
 *
 * <code>
 * use function Laminas\Stratigility\path;
 *
 * $pipeline->pipe(path('/foo', $middleware));
 * </code>
 *
 * @param string $path Path prefix to match in order to dispatch $middleware
 * @return Middleware\PathMiddlewareDecorator
 */
function path($path, MiddlewareInterface $middleware)
{
    return new Middleware\PathMiddlewareDecorator($path, $middleware);
}
