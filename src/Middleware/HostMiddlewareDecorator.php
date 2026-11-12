<?php

declare(strict_types=1);

namespace Laminas\Stratigility\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function strtolower;

final readonly class HostMiddlewareDecorator implements MiddlewareInterface
{
    public function __construct(
        /** @var string Host name under which the middleware is segregated.  */
        private string $host,
        private MiddlewareInterface $middleware
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $host = $request->getUri()->getHost();

        if ($host !== strtolower($this->host)) {
            return $handler->handle($request);
        }

        return $this->middleware->process($request, $handler);
    }
}
