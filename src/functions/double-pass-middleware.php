<?php

declare(strict_types=1);

namespace Laminas\Stratigility;

use Psr\Http\Message\ResponseInterface;

/**
 * Convenience wrapper around instantiation of a DoublePassMiddlewareDecorator instance.
 *
 * Usage:
 *
 * <code>
 * use function Laminas\Stratigility\doublePassMiddleware;
 *
 * $pipeline->pipe(doublePassMiddleware(function ($req, $res, $next) {
 *     // do some work
 * }));
 * </code>
 *
 * Optionally, pass a response prototype as well, if using a PSR-7
 * implementation other than laminas-diactoros:
 *
 * <code>
 * $pipeline->pipe(doublePassMiddleware(function ($req, $res, $next) {
 *     // do some work
 * }, $responsePrototype));
 * </code>
 */
function doublePassMiddleware(
    callable $middleware,
    ?ResponseInterface $response = null
): Middleware\DoublePassMiddlewareDecorator {
    return new Middleware\DoublePassMiddlewareDecorator($middleware, $response);
}
