<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

trait MiddlewareTrait
{
    private function getNotCalledMiddleware(): MiddlewareInterface
    {
        $middleware = $this->createMock(MiddlewareInterface::class);
        $middleware
            ->expects(self::never())
            ->method('process');

        return $middleware;
    }

    private function getPassToHandlerMiddleware(): MiddlewareInterface
    {
        $middleware = $this->createMock(MiddlewareInterface::class);
        $middleware
            ->expects(self::once())
            ->method('process')
            ->willReturnCallback(static fn(ServerRequestInterface $request, RequestHandlerInterface $handler)
                 => $handler->handle($request));

        return $middleware;
    }

    private function getMiddlewareWhichReturnsResponse(ResponseInterface $response): MiddlewareInterface
    {
        $middleware = $this->createMock(MiddlewareInterface::class);
        $middleware
            ->expects(self::once())
            ->method('process')
            ->willReturn($response);

        return $middleware;
    }
}
