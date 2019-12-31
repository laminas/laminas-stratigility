<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Stratigility\Middleware;

use Laminas\Stratigility\Next;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Webimpress\HttpMiddlewareCompatibility\HandlerInterface as DelegateInterface;
use Webimpress\HttpMiddlewareCompatibility\MiddlewareInterface as ServerMiddlewareInterface;

use const Webimpress\HttpMiddlewareCompatibility\HANDLER_METHOD;

/**
 * Decorate legacy callable middleware to make it dispatchable as server
 * middleware.
 *
 * @deprecated since 2.2.0; to be removed in 3.0.0. Use DoublePassMiddlewareDecorator instead.
 */
class CallableMiddlewareWrapper implements ServerMiddlewareInterface
{
    /**
     * @var callable
     */
    private $middleware;

    /**
     * @var ResponseInterface
     */
    private $responsePrototype;

    /**
     * @param callable $middleware
     * @param ResponseInterface $prototype
     */
    public function __construct(callable $middleware, ResponseInterface $prototype)
    {
        $this->middleware = $middleware;
        $this->responsePrototype = $prototype;
    }

    /**
     * Proxies to underlying middleware, using composed response prototype.
     *
     * Also decorates the $delegator using the CallableMiddlewareWrapper.
     *
     * {@inheritDocs}
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $middleware = $this->middleware;
        $delegate = $delegate instanceof Next
            ? $delegate
            : function ($request) use ($delegate) {
                return $delegate->{HANDLER_METHOD}($request);
            };

        return $middleware($request, $this->responsePrototype, $delegate);
    }
}
