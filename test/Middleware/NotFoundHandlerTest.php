<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility\Middleware;

use Laminas\Stratigility\Middleware\NotFoundHandler;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;

class NotFoundHandlerTest extends TestCase
{
    use ProphecyTrait;

    public function testReturnsResponseWith404StatusAndErrorMessageInBody(): void
    {
        $stream = $this->prophesize(StreamInterface::class);
        $stream->write('Cannot POST https://example.com/foo');

        $response = $this->prophesize(ResponseInterface::class);
        $response->withStatus(404)->will([$response, 'reveal']);
        $response->getBody()->will([$stream, 'reveal']);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getMethod()->willReturn('POST');
        $request->getUri()->willReturn('https://example.com/foo');

        $responseFactory = function () use ($response): ResponseInterface {
            return $response->reveal();
        };

        $middleware = new NotFoundHandler($responseFactory);

        $this->assertSame(
            $response->reveal(),
            $middleware->process($request->reveal(), $this->prophesize(RequestHandlerInterface::class)->reveal())
        );
    }
}
