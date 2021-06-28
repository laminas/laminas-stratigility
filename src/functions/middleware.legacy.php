<?php

declare(strict_types=1);

namespace Zend\Stratigility;

use function func_get_args;
use function Laminas\Stratigility\middleware as laminas_middleware;

/**
 * @deprecated Use Laminas\Stratigility\middleware instead
 */
function middleware(callable $middleware): Middleware\CallableMiddlewareDecorator
{
    return laminas_middleware(...func_get_args());
}
