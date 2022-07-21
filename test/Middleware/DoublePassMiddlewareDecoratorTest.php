<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility\Middleware;

use Laminas\Diactoros\Response;
use Laminas\Stratigility\Exception;
use Laminas\Stratigility\Middleware\DoublePassMiddlewareDecorator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function Laminas\Stratigility\doublePassMiddleware;

class DoublePassMiddlewareDecoratorTest extends TestCase
{
    public function testCallableMiddlewareThatDoesNotProduceAResponseRaisesAnException(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $request  = $this->createMock(ServerRequestInterface::class);
        $handler  = $this->createMock(RequestHandlerInterface::class);

        $middleware = static fn($request, $response, $next): string => 'foo';

        $decorator = new DoublePassMiddlewareDecorator($middleware, $response);

        $this->expectException(Exception\MissingResponseException::class);
        $this->expectExceptionMessage('failed to produce a response');
        $decorator->process($request, $handler);
    }

    public function testCallableMiddlewareReturningAResponseSucceedsProcessCall(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $request  = $this->createMock(ServerRequestInterface::class);
        $handler  = $this->createMock(RequestHandlerInterface::class);

        $middleware = static fn($request, $response, $next) => $response;

        $decorator = new DoublePassMiddlewareDecorator($middleware, $response);

        $this->assertSame($response, $decorator->process($request, $handler));
    }

    public function testCallableMiddlewareCanDelegateViaHandler(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $request  = $this->createMock(ServerRequestInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler
            ->expects(self::once())
            ->method('handle')
            ->with($request)
            ->willReturn($response);

        $middleware = /** @psalm-param callable(ServerRequestInterface,ResponseInterface):ResponseInterface $next */
            static fn(ServerRequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface => $next($request, $response);

        $decorator = new DoublePassMiddlewareDecorator($middleware, $response);

        $this->assertSame(
            $response,
            $decorator->process($request, $handler)
        );
    }

    public function testDecoratorCreatesAResponsePrototypeIfNoneIsProvided(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);

        $middleware = static fn($request, $response, $next) => $response;

        $decorator = new DoublePassMiddlewareDecorator($middleware);

        $response = $decorator->process($request, $handler);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testDoublePassMiddlewareFunction(): void
    {
        $toDecorate = static fn($request, $response, $next): string => 'foo';

        $response = $this->createMock(ResponseInterface::class);

        $middleware = doublePassMiddleware($toDecorate, $response);
        self::assertInstanceOf(DoublePassMiddlewareDecorator::class, $middleware);
        self::assertEquals(new DoublePassMiddlewareDecorator($toDecorate, $response), $middleware);
    }
}
