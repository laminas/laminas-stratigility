<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility\Middleware;

use Laminas\Stratigility\Exception;
use Laminas\Stratigility\Middleware\CallableMiddlewareDecorator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function Laminas\Stratigility\middleware;

class CallableMiddlewareDecoratorTest extends TestCase
{
    public function testCallableMiddlewareThatDoesNotProduceAResponseRaisesAnException(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);

        $middleware = function (): string {
            return 'foo';
        };

        $decorator = new CallableMiddlewareDecorator($middleware);

        $this->expectException(Exception\MissingResponseException::class);
        $this->expectExceptionMessage('failed to produce a response');
        $decorator->process($request, $handler);
    }

    public function testCallableMiddlewareReturningAResponseSucceedsProcessCall(): void
    {
        $request  = $this->createMock(ServerRequestInterface::class);
        $handler  = $this->createMock(RequestHandlerInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $middleware = function ($request, $handler) use ($response): ResponseInterface {
            return $response;
        };

        $decorator = new CallableMiddlewareDecorator($middleware);

        $this->assertSame($response, $decorator->process($request, $handler));
    }

    public function testMiddlewareFunction(): void
    {
        $toDecorate = static function (): string {
            return 'foo';
        };

        $middleware = middleware($toDecorate);
        self::assertInstanceOf(CallableMiddlewareDecorator::class, $middleware);
        self::assertEquals(new CallableMiddlewareDecorator($toDecorate), $middleware);
    }
}
