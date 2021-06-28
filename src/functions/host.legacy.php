<?php

declare(strict_types=1);

namespace Zend\Stratigility;

use Psr\Http\Server\MiddlewareInterface;

use function func_get_args;
use function Laminas\Stratigility\host as laminas_host;

/**
 * @deprecated Use Laminas\Stratigility\host instead
 */
function host(string $host, MiddlewareInterface $middleware): Middleware\HostMiddlewareDecorator
{
    return laminas_host(...func_get_args());
}
