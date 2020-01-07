<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Stratigility;

use Psr\Http\Server\MiddlewareInterface;

use function Laminas\Stratigility\host as laminas_host;

/**
 * @deprecated Use Laminas\Stratigility\host instead
 */
function host(string $host, MiddlewareInterface $middleware) : Middleware\HostMiddlewareDecorator
{
    return laminas_host(...func_get_args());
}
