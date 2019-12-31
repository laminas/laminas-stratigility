<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Stratigility;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

/**
 * Dispatch middleware
 *
 * This class is an implementation detail of Next.
 *
 * @internal
 */
class Dispatch
{
    /**
     * Dispatch middleware
     *
     * Given a route (which contains the handler for given middleware),
     * the $err value passed to $next, $next, and the request and response
     * objects, dispatch a middleware handler.
     *
     * If $err is non-falsy, and the current handler has an arity of 4,
     * it will be dispatched.
     *
     * If $err is falsy, and the current handler has an arity of < 4,
     * it will be dispatched.
     *
     * In all other cases, the handler will be ignored, and $next will be
     * invoked with the current $err value.
     *
     * If an exception is raised when executing the handler, the exception
     * will be assigned as the value of $err, and $next will be invoked
     * with it.
     *
     * @param Route $route
     * @param mixed $err
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     */
    public function __invoke(
        Route $route,
        $err,
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $handler  = $route->handler;
        $hasError = (null !== $err);

        switch (true) {
            case ($handler instanceof ErrorMiddlewareInterface):
                $arity = 4;
                break;
            case ($handler instanceof MiddlewareInterface):
                $arity = 3;
                break;
            default:
                $arity = Utils::getArity($handler);
                break;
        }

        // @todo Trigger event with Route, original URL from request?

        try {
            if ($hasError && $arity === 4) {
                return $handler($err, $request, $response, $next);
            }

            if (! $hasError && $arity < 4) {
                return $handler($request, $response, $next);
            }
        } catch (Throwable $throwable) {
            return $next($request, $response, $throwable);
        } catch (Exception $exception) {
            return $next($request, $response, $exception);
        }

        return $next($request, $response, $err);
    }
}
