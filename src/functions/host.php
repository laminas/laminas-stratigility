<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

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
function host(string $host, MiddlewareInterface $middleware) : Middleware\HostMiddlewareDecorator
{
    return new Middleware\HostMiddlewareDecorator($host, $middleware);
}
