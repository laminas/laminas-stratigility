<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility\Handler;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Laminas\Stratigility\Handler\NotFoundHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

class NotFoundHandlerTest extends TestCase
{
    public function testReturnsResponseWith404StatusAndErrorMessageInBody(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request
            ->expects(self::once())
            ->method('getMethod')
            ->willReturn('POST');
        $request
            ->expects(self::once())
            ->method('getUri')
            ->willReturn('https://example.com/foo');

        $stream = $this->createMock(StreamInterface::class);
        $stream
            ->expects(self::once())
            ->method('write')
            ->with('Cannot POST https://example.com/foo')
            ->willReturnSelf();

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects(self::once())
            ->method('getBody')
            ->willReturn($stream);

        $responseFactory = $this->createMock(ResponseFactoryInterface::class);
        $responseFactory
            ->expects(self::once())
            ->method('createResponse')
            ->with(StatusCode::STATUS_NOT_FOUND)
            ->willReturn($response);

        $middleware = new NotFoundHandler($responseFactory);

        $this->assertSame(
            $response,
            $middleware->handle($request)
        );
    }
}
