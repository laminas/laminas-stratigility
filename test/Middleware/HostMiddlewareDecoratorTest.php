<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility\Middleware;

use Generator;
use Laminas\Stratigility\Middleware\HostMiddlewareDecorator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function Laminas\Stratigility\host;

class HostMiddlewareDecoratorTest extends TestCase
{
    /** @var UriInterface&MockObject */
    private $uri;

    /** @var ServerRequestInterface&MockObject */
    private $request;

    /** @var ResponseInterface&MockObject */
    private $response;

    /** @var RequestHandlerInterface&MockObject */
    private $handler;

    /** @var MiddlewareInterface&MockObject */
    private $toDecorate;

    protected function setUp(): void
    {
        $this->uri        = $this->createMock(UriInterface::class);
        $this->request    = $this->createMock(ServerRequestInterface::class);
        $this->response   = $this->createMock(ResponseInterface::class);
        $this->handler    = $this->createMock(RequestHandlerInterface::class);
        $this->toDecorate = $this->createMock(MiddlewareInterface::class);
    }

    public function testImplementsMiddlewareInterface(): void
    {
        $middleware = new HostMiddlewareDecorator('host.test', $this->toDecorate);
        self::assertInstanceOf(MiddlewareInterface::class, $middleware);
    }

    public function testDelegatesOriginalRequestToHandlerIfRequestHostDoesNotMatchDecoratorHostName(): void
    {
        $this->uri
            ->method('getHost')
            ->willReturn('host.foo');
        $this->request
            ->method('getUri')
            ->willReturn($this->uri);

        $this->handler
            ->method('handle')
            ->with($this->request)
            ->willReturn($this->response);

        $this->toDecorate
            ->expects(self::never())
            ->method('process');

        $decorator = new HostMiddlewareDecorator('host.bar', $this->toDecorate);
        $decorator->process($this->request, $this->handler);
    }

    public function matchingHost(): Generator
    {
        yield ['host.foo', 'host.foo'];
        yield ['host.foo', 'HOST.FOO'];
        yield ['host.foo', 'hOsT.fOO'];
    }

    /**
     * @dataProvider matchingHost
     */
    public function testDelegatesOriginalRequestToDecoratedMiddleware(string $requestHost, string $decoratorHost): void
    {
        $this->uri
            ->method('getHost')
            ->willReturn($requestHost);
        $this->request
            ->method('getUri')
            ->willReturn($this->uri);

        $this->handler
            ->expects(self::never())
            ->method('handle');

        $this->toDecorate
            ->expects(self::once())
            ->method('process')
            ->with($this->request, $this->handler)
            ->willReturn($this->response);

        $decorator = new HostMiddlewareDecorator($decoratorHost, $this->toDecorate);
        $decorator->process($this->request, $this->handler);
    }

    public function testHostFunction(): void
    {
        $toDecorate = $this->toDecorate;

        $middleware = host('foo.bar', $toDecorate);
        self::assertEquals(new HostMiddlewareDecorator('foo.bar', $toDecorate), $middleware);
    }
}
