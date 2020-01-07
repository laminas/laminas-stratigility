<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Stratigility;

use Psr\Http\Message\ResponseInterface;

use function Laminas\Stratigility\doublePassMiddleware as laminas_doublePassMiddleware;

/**
 * @deprecated Use Laminas\Stratigility\doublePassMiddleware instead
 */
function doublePassMiddleware(
    callable $middleware,
    ResponseInterface $response = null
) : Middleware\DoublePassMiddlewareDecorator {
    return laminas_doublePassMiddleware(...func_get_args());
}
