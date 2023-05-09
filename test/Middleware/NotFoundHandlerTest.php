<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility\Middleware;

use Laminas\Diactoros\Uri;
use Laminas\Stratigility\Middleware\NotFoundHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;

class NotFoundHandlerTest extends TestCase
{
    public function testReturnsResponseWith404StatusAndErrorMessageInBody(): void
    {
        /** @var StreamInterface&MockObject $stream */
        $stream = $this->createMock(StreamInterface::class);
        $stream
            ->expects(self::once())
            ->method('write')
            ->with('Cannot POST https://example.com/foo')
            ->willReturn(0);

        /** @var ResponseInterface&MockObject $response */
        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('withStatus')
            ->with(404)
            ->willReturnSelf();
        $response
            ->method('getBody')
            ->willReturn($stream);

        /** @var ServerRequestInterface&MockObject $request */
        $request = $this->createMock(ServerRequestInterface::class);
        $request
            ->method('getMethod')
            ->willReturn('POST');

        $uri = new Uri('https://example.com/foo');
        $request
            ->method('getUri')
            ->willReturn($uri);

        $responseFactory = static fn(): ResponseInterface => $response;

        $middleware = new NotFoundHandler($responseFactory);

        $this->assertSame(
            $response,
            $middleware->process($request, $this->createMock(RequestHandlerInterface::class))
        );
    }
}
