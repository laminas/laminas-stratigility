<?php

declare(strict_types=1);

namespace Zend\Stratigility;

use Psr\Http\Server\MiddlewareInterface;

use function func_get_args;
use function Laminas\Stratigility\path as laminas_path;

/**
 * @deprecated Use Laminas\Stratigility\path instead
 */
function path(string $path, MiddlewareInterface $middleware): Middleware\PathMiddlewareDecorator
{
    return laminas_path(...func_get_args());
}
