<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility\Middleware;

use Laminas\Stratigility\Middleware\RequestHandlerMiddleware;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandlerMiddlewareTest extends TestCase
{
    use ProphecyTrait;

    protected function setUp(): void
    {
        $this->request  = $this->prophesize(ServerRequestInterface::class)->reveal();
        $this->response = $this->prophesize(ResponseInterface::class)->reveal();

        $this->handler = $this->prophesize(RequestHandlerInterface::class);
        $this->handler->handle($this->request)->willReturn($this->response);

        $this->middleware = new RequestHandlerMiddleware($this->handler->reveal());
    }

    public function testDecoratesHandlerAsMiddleware()
    {
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler->handle(Argument::any())->shouldNotBeCalled();

        $this->assertSame(
            $this->response,
            $this->middleware->process($this->request, $handler->reveal())
        );
    }

    public function testDecoratesHandlerAsHandler()
    {
        $this->assertSame(
            $this->response,
            $this->middleware->handle($this->request)
        );
    }
}
