<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Stratigility;

use Webimpress\HttpMiddlewareCompatibility\MiddlewareInterface;

use function Laminas\Stratigility\path as laminas_path;

/**
 * @deprecated Use Laminas\Stratigility\path instead
 */
function path($path, MiddlewareInterface $middleware)
{
    return laminas_path(...func_get_args());
}
