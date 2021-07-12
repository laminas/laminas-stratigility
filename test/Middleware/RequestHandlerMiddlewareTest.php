<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility\Middleware;

use Laminas\Stratigility\Middleware\RequestHandlerMiddleware;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandlerMiddlewareTest extends TestCase
{
    /** @var MockObject&ServerRequestInterface */
    private $request;

    /** @var MockObject&ResponseInterface */
    private $response;

    /** @var MockObject&RequestHandlerInterface */
    private $handler;

    /** @var RequestHandlerMiddleware */
    private $middleware;

    protected function setUp(): void
    {
        $this->request  = $this->createMock(ServerRequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);

        $this->handler = $this->createMock(RequestHandlerInterface::class);
        $this->handler
            ->method('handle')
        ->with($this->request)
            ->willReturn($this->response);

        $this->middleware = new RequestHandlerMiddleware($this->handler);
    }

    public function testDecoratesHandlerAsMiddleware(): void
    {
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler
            ->expects(self::never())
            ->method('handle');

        $this->assertSame(
            $this->response,
            $this->middleware->process($this->request, $handler)
        );
    }

    public function testDecoratesHandlerAsHandler(): void
    {
        $this->assertSame(
            $this->response,
            $this->middleware->handle($this->request)
        );
    }
}
