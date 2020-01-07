<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Stratigility;

use Psr\Http\Server\MiddlewareInterface;

use function Laminas\Stratigility\path as laminas_path;

/**
 * @deprecated Use Laminas\Stratigility\path instead
 */
function path(string $path, MiddlewareInterface $middleware) : Middleware\PathMiddlewareDecorator
{
    return laminas_path(...func_get_args());
}
