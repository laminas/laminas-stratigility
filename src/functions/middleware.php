<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Stratigility;

use Psr\Http\Message\ResponseInterface;

/**
 * Convenience wrapper around instantiation of a CallableMiddlewareDecorator instance.
 *
 * Usage:
 *
 * <code>
 * $pipeline->pipe(middleware(function ($req, $handler) {
 *     // do some work
 * }));
 * </code>
 *
 * @return Middleware\CallableMiddlewareDecorator
 */
function middleware(callable $middleware)
{
    return new Middleware\CallableMiddlewareDecorator($middleware);
}
