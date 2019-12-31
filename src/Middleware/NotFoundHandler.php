<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Stratigility\Middleware;

use Laminas\Stratigility\Delegate\CallableDelegateDecorator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Webimpress\HttpMiddlewareCompatibility\HandlerInterface as DelegateInterface;
use Webimpress\HttpMiddlewareCompatibility\MiddlewareInterface as ServerMiddlewareInterface;

class NotFoundHandler implements ServerMiddlewareInterface
{
    /**
     * @var ResponseInterface
     */
    private $responsePrototype;

    /**
     * @param ResponseInterface $responsePrototype Empty/prototype response to
     *     update and return when returning an 404 response.
     */
    public function __construct(ResponseInterface $responsePrototype)
    {
        $this->responsePrototype = $responsePrototype;
    }

    /**
     * Proxy to process()
     *
     * Proxies to process, after first wrapping the `$next` argument using the
     * CallableDelegateDecorator.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        return $this->process($request, new CallableDelegateDecorator($next, $response));
    }

    /**
     * Creates and returns a 404 response.
     *
     * @param ServerRequestInterface $request Ignored.
     * @param DelegateInterface $delegate Ignored.
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $response = $this->responsePrototype
            ->withStatus(404);
        $response->getBody()->write(sprintf(
            'Cannot %s %s',
            $request->getMethod(),
            (string) $request->getUri()
        ));
        return $response;
    }
}
