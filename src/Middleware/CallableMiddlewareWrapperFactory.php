<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Stratigility\Middleware;

use Psr\Http\Message\ResponseInterface;

/**
 * @deprecated since 2.2.0; to be removed in 3.0.0.
 */
class CallableMiddlewareWrapperFactory
{
    /**
     * @var ResponseInterface
     */
    private $responsePrototype;

    /**
     * @param ResponseInterface $prototype
     */
    public function __construct(ResponseInterface $prototype)
    {
        $this->responsePrototype = $prototype;
    }

    /**
     * @param callable $middleware
     * @return CallableMiddlewareWrapper
     */
    public function decorateCallableMiddleware(callable $middleware)
    {
        return new CallableMiddlewareWrapper($middleware, $this->responsePrototype);
    }
}
