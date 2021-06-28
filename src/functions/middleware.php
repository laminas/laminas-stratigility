<?php

declare(strict_types=1);

namespace Laminas\Stratigility;

/**
 * Convenience wrapper around instantiation of a CallableMiddlewareDecorator instance.
 *
 * Usage:
 *
 * <code>
 * use function Laminas\Stratigility\middleware;
 *
 * $pipeline->pipe(middleware(function ($req, $handler) {
 *     // do some work
 * }));
 * </code>
 */
function middleware(callable $middleware): Middleware\CallableMiddlewareDecorator
{
    return new Middleware\CallableMiddlewareDecorator($middleware);
}
