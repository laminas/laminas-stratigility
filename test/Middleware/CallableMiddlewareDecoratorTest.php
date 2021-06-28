<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility\Middleware;

use Laminas\Stratigility\Exception;
use Laminas\Stratigility\Middleware\CallableMiddlewareDecorator;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function Laminas\Stratigility\middleware;

class CallableMiddlewareDecoratorTest extends TestCase
{
    use ProphecyTrait;

    public function testCallableMiddlewareThatDoesNotProduceAResponseRaisesAnException(): void
    {
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $handler = $this->prophesize(RequestHandlerInterface::class)->reveal();

        $middleware = function ($request, $handler): string {
            return 'foo';
        };

        $decorator = new CallableMiddlewareDecorator($middleware);

        $this->expectException(Exception\MissingResponseException::class);
        $this->expectExceptionMessage('failed to produce a response');
        $decorator->process($request, $handler);
    }

    public function testCallableMiddlewareReturningAResponseSucceedsProcessCall(): void
    {
        $request  = $this->prophesize(ServerRequestInterface::class)->reveal();
        $handler  = $this->prophesize(RequestHandlerInterface::class)->reveal();
        $response = $this->prophesize(ResponseInterface::class)->reveal();

        $middleware = function ($request, $handler) use ($response): ResponseInterface {
            return $response;
        };

        $decorator = new CallableMiddlewareDecorator($middleware);

        $this->assertSame($response, $decorator->process($request, $handler));
    }

    public function testMiddlewareFunction(): void
    {
        $toDecorate = function ($request, $handler): string {
            return 'foo';
        };

        $middleware = middleware($toDecorate);
        self::assertInstanceOf(CallableMiddlewareDecorator::class, $middleware);
        self::assertEquals(new CallableMiddlewareDecorator($toDecorate), $middleware);
    }
}
