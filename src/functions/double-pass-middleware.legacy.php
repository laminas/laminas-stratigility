<?php

declare(strict_types=1);

namespace Zend\Stratigility;

use Psr\Http\Message\ResponseInterface;

use function func_get_args;
use function Laminas\Stratigility\doublePassMiddleware as laminas_doublePassMiddleware;

/**
 * @deprecated Use Laminas\Stratigility\doublePassMiddleware instead
 */
function doublePassMiddleware(
    callable $middleware,
    ?ResponseInterface $response = null
): Middleware\DoublePassMiddlewareDecorator {
    return laminas_doublePassMiddleware(...func_get_args());
}
