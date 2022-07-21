<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility\Middleware;

use Laminas\Stratigility\Middleware\NotFoundHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;

class NotFoundHandlerTest extends TestCase
{
    public function testReturnsResponseWith404StatusAndErrorMessageInBody(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream
            ->expects(self::once())
            ->method('write')
            ->with('Cannot POST https://example.com/foo')
            ->willReturnSelf();

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('withStatus')
            ->with(404)
            ->willReturnSelf();
        $response
            ->method('getBody')
            ->willReturn($stream);

        $request = $this->createMock(ServerRequestInterface::class);
        $request
            ->method('getMethod')
            ->willReturn('POST');
        $request
            ->method('getUri')
            ->willReturn('https://example.com/foo');

        $responseFactory = static fn(): ResponseInterface => $response;

        $middleware = new NotFoundHandler($responseFactory);

        $this->assertSame(
            $response,
            $middleware->process($request, $this->createMock(RequestHandlerInterface::class))
        );
    }
}
