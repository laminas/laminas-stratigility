<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility\Handler;

use Laminas\Stratigility\Handler\NotFoundHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

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
            ->expects(self::once())
            ->method('withStatus')
            ->with(404)
            ->willReturnSelf();
        $response
            ->expects(self::once())
            ->method('getBody')
            ->willReturn($stream);

        $request = $this->createMock(ServerRequestInterface::class);
        $request
            ->expects(self::once())
            ->method('getMethod')
            ->willReturn('POST');
        $request
            ->expects(self::once())
            ->method('getUri')
            ->willReturn('https://example.com/foo');

        $responseFactory = static fn(): ResponseInterface => $response;

        $middleware = new NotFoundHandler($responseFactory);

        $this->assertSame(
            $response,
            $middleware->handle($request)
        );
    }
}
