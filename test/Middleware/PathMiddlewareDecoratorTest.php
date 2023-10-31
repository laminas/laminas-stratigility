<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility\Middleware;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use Laminas\Stratigility\Middleware\PathMiddlewareDecorator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function Laminas\Stratigility\middleware;
use function Laminas\Stratigility\path;
use function sprintf;
use function var_export;

class PathMiddlewareDecoratorTest extends TestCase
{
    /** @var MockObject&UriInterface */
    private $uri;

    /** @var MockObject&ServerRequestInterface */
    private $request;

    /** @var MockObject&ResponseInterface */
    private $response;

    /** @var MockObject&RequestHandlerInterface */
    private $handler;

    /** @var MockObject&MiddlewareInterface */
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
        $middleware = new PathMiddlewareDecorator('/foo', $this->toDecorate);
        $this->assertInstanceOf(MiddlewareInterface::class, $middleware);
    }

    public function testDelegatesOriginalRequestToHandlerIfRequestPathIsShorterThanDecoratorPrefix(): void
    {
        $this->uri
            ->method('getPath')
            ->willReturn('/f');
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

        $middleware = new PathMiddlewareDecorator('/foo', $this->toDecorate);

        $this->assertSame(
            $this->response,
            $middleware->process($this->request, $this->handler)
        );
    }

    public function testDelegatesOriginalRequestToHandlerIfRequestPathIsDoesNotMatchDecoratorPath(): void
    {
        $this->uri
            ->method('getPath')
            ->willReturn('/bar');
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

        $middleware = new PathMiddlewareDecorator('/foo', $this->toDecorate);
        $middleware->process($this->request, $this->handler);
    }

    public function testDelegatesOrignalRequestToHandlerIfRequestDoesNotMatchPrefixAtABoundary(): void
    {
        // e.g., if route is "/foo", but path is "/foobar", no match
        $uri     = (new Uri())->withPath('/foobar');
        $request = (new ServerRequest())->withUri($uri);
        self::assertInstanceOf(ServerRequestInterface::class, $request);
        $response = new Response();

        $middleware = $this->createMock(MiddlewareInterface::class);
        $middleware
            ->expects(self::never())
            ->method('process');

        $decorator = new PathMiddlewareDecorator('/foo', $middleware);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler
            ->method('handle')
            ->with($request)
            ->willReturn($response);

        $this->assertSame(
            $response,
            $decorator->process($request, $handler)
        );
    }

    /**
     * @psalm-return array<string, array{
     *     0: string,
     *     1: string,
     *     2: string,
     *     3: bool
     * }>
     */
    public static function nestedPathCombinations(): array
    {
        return [
            // name                      => [$prefix, $nestPrefix, $uriPath,      $expectsHeader ]
            'empty-bare-bare'            => ['',       'foo',    '/foo',          true],
            'empty-bare-bareplus'        => ['',       'foo',    '/foobar',       false],
            'empty-bare-tail'            => ['',       'foo',    '/foo/',         true],
            'empty-bare-tailplus'        => ['',       'foo',    '/foo/bar',      true],
            'empty-tail-bare'            => ['',       'foo/',   '/foo',          true],
            'empty-tail-bareplus'        => ['',       'foo/',   '/foobar',       false],
            'empty-tail-tail'            => ['',       'foo/',   '/foo/',         true],
            'empty-tail-tailplus'        => ['',       'foo/',   '/foo/bar',      true],
            'empty-prefix-bare'          => ['',       '/foo',   '/foo',          true],
            'empty-prefix-bareplus'      => ['',       '/foo',   '/foobar',       false],
            'empty-prefix-tail'          => ['',       '/foo',   '/foo/',         true],
            'empty-prefix-tailplus'      => ['',       '/foo',   '/foo/bar',      true],
            'empty-surround-bare'        => ['',       '/foo/',  '/foo',          true],
            'empty-surround-bareplus'    => ['',       '/foo/',  '/foobar',       false],
            'empty-surround-tail'        => ['',       '/foo/',  '/foo/',         true],
            'empty-surround-tailplus'    => ['',       '/foo/',  '/foo/bar',      true],
            'root-bare-bare'             => ['/',      'foo',    '/foo',          true],
            'root-bare-bareplus'         => ['/',      'foo',    '/foobar',       false],
            'root-bare-tail'             => ['/',      'foo',    '/foo/',         true],
            'root-bare-tailplus'         => ['/',      'foo',    '/foo/bar',      true],
            'root-tail-bare'             => ['/',      'foo/',   '/foo',          true],
            'root-tail-bareplus'         => ['/',      'foo/',   '/foobar',       false],
            'root-tail-tail'             => ['/',      'foo/',   '/foo/',         true],
            'root-tail-tailplus'         => ['/',      'foo/',   '/foo/bar',      true],
            'root-prefix-bare'           => ['/',      '/foo',   '/foo',          true],
            'root-prefix-bareplus'       => ['/',      '/foo',   '/foobar',       false],
            'root-prefix-tail'           => ['/',      '/foo',   '/foo/',         true],
            'root-prefix-tailplus'       => ['/',      '/foo',   '/foo/bar',      true],
            'root-surround-bare'         => ['/',      '/foo/',  '/foo',          true],
            'root-surround-bareplus'     => ['/',      '/foo/',  '/foobar',       false],
            'root-surround-tail'         => ['/',      '/foo/',  '/foo/',         true],
            'root-surround-tailplus'     => ['/',      '/foo/',  '/foo/bar',      true],
            'bare-bare-bare'             => ['foo',    'bar',    '/foo/bar',      true],
            'bare-bare-bareplus'         => ['foo',    'bar',    '/foo/barbaz',   false],
            'bare-bare-tail'             => ['foo',    'bar',    '/foo/bar/',     true],
            'bare-bare-tailplus'         => ['foo',    'bar',    '/foo/bar/baz',  true],
            'bare-tail-bare'             => ['foo',    'bar/',   '/foo/bar',      true],
            'bare-tail-bareplus'         => ['foo',    'bar/',   '/foo/barbaz',   false],
            'bare-tail-tail'             => ['foo',    'bar/',   '/foo/bar/',     true],
            'bare-tail-tailplus'         => ['foo',    'bar/',   '/foo/bar/baz',  true],
            'bare-prefix-bare'           => ['foo',    '/bar',   '/foo/bar',      true],
            'bare-prefix-bareplus'       => ['foo',    '/bar',   '/foo/barbaz',   false],
            'bare-prefix-tail'           => ['foo',    '/bar',   '/foo/bar/',     true],
            'bare-prefix-tailplus'       => ['foo',    '/bar',   '/foo/bar/baz',  true],
            'bare-surround-bare'         => ['foo',    '/bar/',  '/foo/bar',      true],
            'bare-surround-bareplus'     => ['foo',    '/bar/',  '/foo/barbaz',   false],
            'bare-surround-tail'         => ['foo',    '/bar/',  '/foo/bar/',     true],
            'bare-surround-tailplus'     => ['foo',    '/bar/',  '/foo/bar/baz',  true],
            'tail-bare-bare'             => ['foo/',   'bar',    '/foo/bar',      true],
            'tail-bare-bareplus'         => ['foo/',   'bar',    '/foo/barbaz',   false],
            'tail-bare-tail'             => ['foo/',   'bar',    '/foo/bar/',     true],
            'tail-bare-tailplus'         => ['foo/',   'bar',    '/foo/bar/baz',  true],
            'tail-tail-bare'             => ['foo/',   'bar/',   '/foo/bar',      true],
            'tail-tail-bareplus'         => ['foo/',   'bar/',   '/foo/barbaz',   false],
            'tail-tail-tail'             => ['foo/',   'bar/',   '/foo/bar/',     true],
            'tail-tail-tailplus'         => ['foo/',   'bar/',   '/foo/bar/baz',  true],
            'tail-prefix-bare'           => ['foo/',   '/bar',   '/foo/bar',      true],
            'tail-prefix-bareplus'       => ['foo/',   '/bar',   '/foo/barbaz',   false],
            'tail-prefix-tail'           => ['foo/',   '/bar',   '/foo/bar/',     true],
            'tail-prefix-tailplus'       => ['foo/',   '/bar',   '/foo/bar/baz',  true],
            'tail-surround-bare'         => ['foo/',   '/bar/',  '/foo/bar',      true],
            'tail-surround-bareplus'     => ['foo/',   '/bar/',  '/foo/barbaz',   false],
            'tail-surround-tail'         => ['foo/',   '/bar/',  '/foo/bar/',     true],
            'tail-surround-tailplus'     => ['foo/',   '/bar/',  '/foo/bar/baz',  true],
            'prefix-bare-bare'           => ['/foo',   'bar',    '/foo/bar',      true],
            'prefix-bare-bareplus'       => ['/foo',   'bar',    '/foo/barbaz',   false],
            'prefix-bare-tail'           => ['/foo',   'bar',    '/foo/bar/',     true],
            'prefix-bare-tailplus'       => ['/foo',   'bar',    '/foo/bar/baz',  true],
            'prefix-tail-bare'           => ['/foo',   'bar/',   '/foo/bar',      true],
            'prefix-tail-bareplus'       => ['/foo',   'bar/',   '/foo/barbaz',   false],
            'prefix-tail-tail'           => ['/foo',   'bar/',   '/foo/bar/',     true],
            'prefix-tail-tailplus'       => ['/foo',   'bar/',   '/foo/bar/baz',  true],
            'prefix-prefix-bare'         => ['/foo',   '/bar',   '/foo/bar',      true],
            'prefix-prefix-bareplus'     => ['/foo',   '/bar',   '/foo/barbaz',   false],
            'prefix-prefix-tail'         => ['/foo',   '/bar',   '/foo/bar/',     true],
            'prefix-prefix-tailplus'     => ['/foo',   '/bar',   '/foo/bar/baz',  true],
            'prefix-surround-bare'       => ['/foo',   '/bar/',  '/foo/bar',      true],
            'prefix-surround-bareplus'   => ['/foo',   '/bar/',  '/foo/barbaz',   false],
            'prefix-surround-tail'       => ['/foo',   '/bar/',  '/foo/bar/',     true],
            'prefix-surround-tailplus'   => ['/foo',   '/bar/',  '/foo/bar/baz',  true],
            'surround-bare-bare'         => ['/foo/',  'bar',    '/foo/bar',      true],
            'surround-bare-bareplus'     => ['/foo/',  'bar',    '/foo/barbaz',   false],
            'surround-bare-tail'         => ['/foo/',  'bar',    '/foo/bar/',     true],
            'surround-bare-tailplus'     => ['/foo/',  'bar',    '/foo/bar/baz',  true],
            'surround-tail-bare'         => ['/foo/',  'bar/',   '/foo/bar',      true],
            'surround-tail-bareplus'     => ['/foo/',  'bar/',   '/foo/barbaz',   false],
            'surround-tail-tail'         => ['/foo/',  'bar/',   '/foo/bar/',     true],
            'surround-tail-tailplus'     => ['/foo/',  'bar/',   '/foo/bar/baz',  true],
            'surround-prefix-bare'       => ['/foo/',  '/bar',   '/foo/bar',      true],
            'surround-prefix-bareplus'   => ['/foo/',  '/bar',   '/foo/barbaz',   false],
            'surround-prefix-tail'       => ['/foo/',  '/bar',   '/foo/bar/',     true],
            'surround-prefix-tailplus'   => ['/foo/',  '/bar',   '/foo/bar/baz',  true],
            'surround-surround-bare'     => ['/foo/',  '/bar/',  '/foo/bar',      true],
            'surround-surround-bareplus' => ['/foo/',  '/bar/',  '/foo/barbaz',   false],
            'surround-surround-tail'     => ['/foo/',  '/bar/',  '/foo/bar/',     true],
            'surround-surround-tailplus' => ['/foo/',  '/bar/',  '/foo/bar/baz',  true],
        ];
    }

    /**
     * @dataProvider nestedPathCombinations
     */
    public function testNestedMiddlewareOnlyMatchesAtPathBoundaries(
        string $prefix,
        string $nestPrefix,
        string $uriPath,
        bool $expectsHeader
    ): void {
        $finalHandler = $this->createMock(RequestHandlerInterface::class);
        $finalHandler
            ->method('handle')
            ->willReturn(new Response());

        $nested = new PathMiddlewareDecorator($nestPrefix, new class () implements MiddlewareInterface {
            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $handler
            ): ResponseInterface {
                return (new Response())->withHeader('X-Found', 'true');
            }
        });

        $topLevel = new PathMiddlewareDecorator($prefix, new class ($nested) implements MiddlewareInterface {
            private MiddlewareInterface $middleware;

            public function __construct(MiddlewareInterface $middleware)
            {
                $this->middleware = $middleware;
            }

            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $handler
            ): ResponseInterface {
                return $this->middleware->process($request, $handler);
            }
        });

        $uri     = (new Uri())->withPath($uriPath);
        $request = (new ServerRequest())->withUri($uri);
        self::assertInstanceOf(ServerRequestInterface::class, $request);

        $response = $topLevel->process($request, $finalHandler);

        $this->assertSame(
            $expectsHeader,
            $response->hasHeader('X-Found'),
            sprintf(
                'Failed with full path %s against top-level prefix "%s" and nested prefix "%s"; expected %s',
                $uriPath,
                $prefix,
                $nestPrefix,
                var_export($expectsHeader, true)
            )
        );
    }

    /** @psalm-return array<array-key, array{0: string}> */
    public static function rootPathsProvider(): array
    {
        return [
            'empty' => [''],
            'root'  => ['/'],
        ];
    }

    /**
     * @group matching
     * @dataProvider rootPathsProvider
     */
    public function testTreatsBothSlashAndEmptyPathAsTheRootPath(string $path): void
    {
        $finalHandler = $this->createMock(RequestHandlerInterface::class);
        $finalHandler
            ->method('handle')
            ->willReturn(new Response());

        $middleware = new PathMiddlewareDecorator($path, new class () implements MiddlewareInterface {
            public function process(ServerRequestInterface $req, RequestHandlerInterface $handler): ResponseInterface
            {
                $res = new Response();
                return $res->withHeader('X-Found', 'true');
            }
        });
        $uri        = (new Uri())->withPath($path);
        $request    = (new ServerRequest())->withUri($uri);
        self::assertInstanceOf(ServerRequestInterface::class, $request);

        $response = $middleware->process($request, $finalHandler);
        $this->assertTrue($response->hasHeader('x-found'));
    }

    public function testRequestPathPassedToDecoratedMiddlewareTrimsPathPrefix(): void
    {
        $finalHandler = $this->createMock(RequestHandlerInterface::class);
        $finalHandler
            ->method('handle')
            ->willReturn(new Response());

        $request = new ServerRequest([], [], 'http://local.example.com/foo/bar', 'GET', 'php://memory');

        $middleware = $this->createMock(MiddlewareInterface::class);
        $middleware
            ->expects(self::once())
            ->method('process')
            ->with(
                self::callback(static function (ServerRequestInterface $req): bool {
                    self::assertSame('/bar', $req->getUri()->getPath());
                    return true;
                })
            )
            ->willReturn(new Response());

        $decorator = new PathMiddlewareDecorator('/foo', $middleware);
        $decorator->process($request, $finalHandler);
    }

    public function testInvocationOfHandlerByDecoratedMiddlewareWillInvokeWithOriginalRequestPath(): void
    {
        $request          = new ServerRequest([], [], 'http://local.example.com/test', 'GET', 'php://memory');
        $expectedResponse = new Response();

        $finalHandler = $this->createMock(RequestHandlerInterface::class);
        $finalHandler
            ->method('handle')
            ->with(
                self::callback(static function ($received) use ($request) {
                    self::assertNotSame(
                        $request,
                        $received,
                        'Final handler received same request, and should not have'
                    );
                    self::assertSame(
                        $request->getUri()->getPath(),
                        $received->getUri()->getPath(),
                        'Final handler received request with different path'
                    );
                    return true;
                })
            )
            ->willReturn($expectedResponse);

        $segregatedMiddleware = $this->createMock(MiddlewareInterface::class);
        $segregatedMiddleware
            ->method('process')
            ->with(
                self::callback(static function ($received) use ($request) {
                    Assert::assertNotSame(
                        $request,
                        $received,
                        'Segregated middleware received same request as decorator, but should not have'
                    );
                    return true;
                })
            )
            ->willReturnCallback(
                static fn(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface =>
                    $next->handle($request)
            );

        $decoratedMiddleware = new PathMiddlewareDecorator('/test', $segregatedMiddleware);

        $this->assertSame(
            $expectedResponse,
            $decoratedMiddleware->process($request, $finalHandler)
        );
    }

    public function testPathFunction(): void
    {
        $toDecorate = $this->toDecorate;
        $middleware = path('/foo', $toDecorate);
        self::assertEquals(new PathMiddlewareDecorator('/foo', $toDecorate), $middleware);
    }

    public function testUpdatesInPathInsideNestedMiddlewareAreRespected(): void
    {
        $request             = new ServerRequest([], [], 'http://local.example.com/foo/bar', 'GET', 'php://memory');
        $decoratedMiddleware = middleware(static fn(ServerRequestInterface $request, RequestHandlerInterface $handler)
            => $handler->handle($request->withUri(new Uri('/changed/path'))));
        $middleware          = new PathMiddlewareDecorator('/foo', $decoratedMiddleware);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler
            ->method('handle')
        ->with(
            self::callback(static function (ServerRequestInterface $received): bool {
                self::assertEquals('/foo/changed/path', $received->getUri()->getPath());
                return true;
            })
        );

        $middleware->process($request, $handler);
    }

    public function testProcessesMatchedPathsWithoutCaseSensitivity(): void
    {
        $finalHandler = $this->createMock(RequestHandlerInterface::class);
        $finalHandler
            ->method('handle')
        ->willReturn(new Response());

        // Note that the path requested is ALL CAPS:
        $request = new ServerRequest([], [], 'http://local.example.com/MYADMIN', 'GET', 'php://memory');

        $middleware = $this->createMock(MiddlewareInterface::class);
        $middleware
            ->expects(self::once())
            ->method('process')
            ->with(
                self::callback(static function (ServerRequestInterface $req) {
                    Assert::assertSame('', $req->getUri()->getPath());
                    return true;
                })
            )
            ->willReturn(new Response());

        // Note that the path to match is lowercase:
        $decorator = new PathMiddlewareDecorator('/myadmin', $middleware);
        $decorator->process($request, $finalHandler);
    }
}
