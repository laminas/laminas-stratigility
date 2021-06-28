<?php

declare(strict_types=1);

namespace Laminas\Stratigility;

use Psr\Http\Server\MiddlewareInterface;

/**
 * Convenience function for creating host-segregated middleware.
 *
 * Usage:
 *
 * <code>
 * use function Laminas\Stratigility\host;
 *
 * $pipeline->pipe(host('host.foo', $middleware));
 * </code>
 */
function host(string $host, MiddlewareInterface $middleware): Middleware\HostMiddlewareDecorator
{
    return new Middleware\HostMiddlewareDecorator($host, $middleware);
}
