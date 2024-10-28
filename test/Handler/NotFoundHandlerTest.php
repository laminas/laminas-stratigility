<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility\Handler;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Laminas\Diactoros\Uri;
use Laminas\Stratigility\Handler\NotFoundHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

class NotFoundHandlerTest extends TestCase
{
    public function testReturnsResponseWith404StatusAndErrorMessageInBody(): void
    {
        /** @var ServerRequestInterface&MockObject $request */
        $request = $this->createMock(ServerRequestInterface::class);
        $request
            ->expects(self::once())
            ->method('getMethod')
            ->willReturn('POST');

        $uri = new Uri('https://example.org/foo');
        $request
            ->expects(self::once())
            ->method('getUri')
            ->willReturn($uri);

        /** @var StreamInterface&MockObject $stream */
        $stream = $this->createMock(StreamInterface::class);
        $stream
            ->expects(self::once())
            ->method('write')
            ->with('Cannot POST https://example.org/foo')
            ->willReturn(35);

        /** @var ResponseInterface&MockObject $response */
        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects(self::once())
            ->method('getBody')
            ->willReturn($stream);

        /** @var ResponseFactoryInterface&MockObject $responseFactory */
        $responseFactory = $this->createMock(ResponseFactoryInterface::class);
        $responseFactory
            ->expects(self::once())
            ->method('createResponse')
            ->with(StatusCode::STATUS_NOT_FOUND, '')
            ->willReturn($response);

        $middleware = new NotFoundHandler($responseFactory);

        $this->assertSame(
            $response,
            $middleware->handle($request)
        );
    }
}
