<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility\Middleware;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use Laminas\Stratigility\Middleware\PathMiddlewareDecorator;
use Laminas\Stratigility\MiddlewarePipe;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PathMiddlewareDecoratorIntegrationTest extends TestCase
{
    public function testPipelineComposingPathDecoratedMiddlewareExecutesAsExpected(): void
    {
        $uri     = (new Uri())->withPath('/foo/bar/baz');
        $request = (new ServerRequest())->withUri($uri);
        self::assertInstanceOf(ServerRequestInterface::class, $request);
        $response = new Response();

        $pipeline = new MiddlewarePipe();

        $first  = $this->createPassThroughMiddleware(static function ($received) use ($request) {
            self::assertSame(
                $request,
                $received,
                'First middleware did not receive original request, but should have'
            );
            return $request;
        });
        $second = new PathMiddlewareDecorator('/foo', $this->createNestedPipeline($request));
        $last   = $this->createPassThroughMiddleware(static function ($received) use ($request) {
            self::assertNotSame(
                $request,
                $received,
                'Last middleware received original request, but should not have'
            );
            $originalUri = $request->getUri();
            $receivedUri = $received->getUri();
            Assert::assertNotSame(
                $originalUri,
                $receivedUri,
                'Last middleware received original URI instance, but should not have'
            );
            Assert::assertSame(
                $originalUri->getPath(),
                $receivedUri->getPath(),
                'Last middleware did not receive original URI path, but should have'
            );
            return $request;
        });

        $pipeline->pipe($first);
        $pipeline->pipe($second);
        $pipeline->pipe($last);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler
            ->method('handle')
            ->with($request)
            ->willReturn($response);

        $this->assertSame(
            $response,
            $pipeline->process($request, $handler)
        );
    }

    /**
     * @param callable(mixed) $requestAssertion
     */
    private function createPassThroughMiddleware(callable $requestAssertion): MiddlewareInterface
    {
        $middleware = $this->createMock(MiddlewareInterface::class);
        $middleware
            ->method('process')
            ->with(
                self::callback(
                /** @psalm-suppress MissingClosureParamType */
                    static function ($argument1) use ($requestAssertion): bool {
                        $requestAssertion($argument1);
                        return true;
                    }
                ),
                self::callback(
                /** @psalm-suppress MissingClosureParamType */
                    static function ($argument2): bool {
                        self::assertInstanceOf(RequestHandlerInterface::class, $argument2);
                        return true;
                    }
                )
            )
            ->willReturnCallback(
                static fn(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
                    => $next->handle($request)
            );

        return $middleware;
    }

    public function createNestedPipeline(ServerRequestInterface $originalRequest): MiddlewareInterface
    {
        $pipeline = new MiddlewarePipe();

        $barMiddleware = $this->createMock(MiddlewareInterface::class);
        $barMiddleware
            ->method('process')
            ->with(
                self::callback(
                    static function ($request) use ($originalRequest) {
                        self::assertNotSame(
                            $originalRequest,
                            $request,
                            'Decorated middleware received original request, but should not have'
                        );
                        $path = $request->getUri()->getPath();
                        self::assertSame(
                            '/baz',
                            $path,
                            'Decorated middleware expected path "/baz"; received ' . $path
                        );
                        return true;
                    }
                )
            )
            ->willReturnCallback(static fn(
                ServerRequestInterface $request,
                RequestHandlerInterface $next): ResponseInterface => $next->handle($request));

        $decorated = new PathMiddlewareDecorator('/bar', $barMiddleware);

        $normal = $this->createMock(MiddlewareInterface::class);
        $normal
            ->method('process')
            ->with(
                self::callback(static function (ServerRequestInterface $request) use ($originalRequest): bool {
                    self::assertNotSame(
                        $originalRequest,
                        $request,
                        'Decorated middleware received original request, but should not have'
                    );
                    $path = $request->getUri()->getPath();
                    self::assertSame(
                        '/bar/baz',
                        $path,
                        'Decorated middleware expected path "/bar/baz"; received ' . $path
                    );
                    return true;
                })
            )
            ->willReturnCallback(
                static fn(
                    ServerRequestInterface $request,
                    RequestHandlerInterface $next): ResponseInterface => $next->handle($request)
            );

        $pipeline->pipe($decorated);
        $pipeline->pipe($normal);

        return $pipeline;
    }
}
