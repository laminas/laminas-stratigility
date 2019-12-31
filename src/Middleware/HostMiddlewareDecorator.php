<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Stratigility\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function strtolower;

final class HostMiddlewareDecorator implements MiddlewareInterface
{
    /** @var MiddlewareInterface */
    private $middleware;

    /** @var string Host name under which the middleware is segregated.  */
    private $host;

    public function __construct(string $host, MiddlewareInterface $middleware)
    {
        $this->host = $host;
        $this->middleware = $middleware;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $host = $request->getUri()->getHost();

        if ($host !== strtolower($this->host)) {
            return $handler->handle($request);
        }

        return $this->middleware->process($request, $handler);
    }
}
