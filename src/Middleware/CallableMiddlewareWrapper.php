<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Stratigility\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Laminas\Stratigility\Next;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Decorate legacy callable middleware to make it dispatchable as server
 * middleware.
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
                return $delegate->process($request);
            };

        return $middleware($request, $this->responsePrototype, $delegate);
    }
}
