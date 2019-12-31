<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Stratigility\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Webimpress\HttpMiddlewareCompatibility\HandlerInterface as DelegateInterface;
use Webimpress\HttpMiddlewareCompatibility\MiddlewareInterface as ServerMiddlewareInterface;

/**
 * @deprecated since 2.2.0; to be removed in 3.0.0. Use CallableMiddlewareDecorator instead.
 */
class CallableInteropMiddlewareWrapper implements ServerMiddlewareInterface
{
    /**
     * @param callable
     */
    private $middleware;

    /**
     * @param callable $middleware
     */
    public function __construct(callable $middleware)
    {
        $this->middleware = $middleware;
    }

    /**
     * {@inheritDocs}
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $middleware = $this->middleware;
        return $middleware($request, $delegate);
    }
}
