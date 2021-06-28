<?php

declare(strict_types=1);

namespace Laminas\Stratigility;

use Psr\Http\Server\MiddlewareInterface;

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
 */
function path(string $path, MiddlewareInterface $middleware): Middleware\PathMiddlewareDecorator
{
    return new Middleware\PathMiddlewareDecorator($path, $middleware);
}
