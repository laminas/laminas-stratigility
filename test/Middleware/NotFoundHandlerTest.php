<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Stratigility\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Laminas\Stratigility\Middleware\NotFoundHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

class NotFoundHandlerTest extends TestCase
{
    public function testReturnsResponseWith404StatusAndErrorMessageInBody()
    {
        $stream = $this->prophesize(StreamInterface::class);
        $stream->write('Cannot POST https://example.com/foo');

        $response = $this->prophesize(ResponseInterface::class);
        $response->withStatus(404)->will([$response, 'reveal']);
        $response->getBody()->will([$stream, 'reveal']);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getMethod()->willReturn('POST');
        $request->getUri()->willReturn('https://example.com/foo');

        $middleware = new NotFoundHandler($response->reveal());

        $this->assertSame(
            $response->reveal(),
            $middleware->process($request->reveal(), $this->prophesize(DelegateInterface::class)->reveal())
        );
    }
}
