<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Stratigility;

use Psr\Http\Message\ResponseInterface;

use function Laminas\Stratigility\middleware as laminas_middleware;

/**
 * @deprecated Use Laminas\Stratigility\middleware instead
 */
function middleware(callable $middleware)
{
    return laminas_middleware(...func_get_args());
}
