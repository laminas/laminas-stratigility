<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 */

declare(strict_types=1);

namespace LaminasTest\Stratigility\Middleware;

use Laminas\Stratigility\Middleware\OriginalMessages;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OriginalMessagesTest extends TestCase
{
    use ProphecyTrait;

    protected function setUp(): void
    {
        $this->uri     = $this->prophesize(UriInterface::class);
        $this->request = $this->prophesize(ServerRequestInterface::class);
    }

    public function testNextReceivesRequestWithNewAttributes()
    {
        $middleware = new OriginalMessages();
        $expected   = $this->prophesize(ResponseInterface::class)->reveal();

        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler->handle($this->request->reveal())->willReturn($expected);

        $this->request->getUri()->will([$this->uri, 'reveal']);
        $this->request->withAttribute(
            'originalUri',
            Argument::that(function ($arg) {
                $this->assertSame($this->uri->reveal(), $arg);
                return $arg;
            })
        )->will([$this->request, 'reveal']);

        $this->request->withAttribute(
            'originalRequest',
            Argument::that(function ($arg) {
                $this->assertSame($this->request->reveal(), $arg);
                return $arg;
            })
        )->will([$this->request, 'reveal']);

        $response = $middleware->process($this->request->reveal(), $handler->reveal());

        $this->assertSame($expected, $response);
    }
}
