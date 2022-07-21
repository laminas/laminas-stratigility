<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest as Request;
use Laminas\Diactoros\Uri;
use Laminas\Stratigility\Exception\MiddlewarePipeNextHandlerAlreadyCalledException;
use Laminas\Stratigility\Next;
use LaminasTest\Stratigility\TestAsset\DelegatingMiddleware;
use LaminasTest\Stratigility\TestAsset\ShortCircuitingMiddleware;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplQueue;

class NextTest extends TestCase
{
    use MiddlewareTrait;

    private SplQueue $queue;

    private Request $request;

    /** @var MockObject&ResponseInterface */
    private $response;

    protected function setUp(): void
    {
        $this->queue           = new SplQueue();
        $this->request         = new Request([], [], 'http://example.com/', 'GET', 'php://memory');
        $this->fallbackHandler = $this->createFallbackHandler();
    }

    public function createFallbackHandler(?ResponseInterface $response = null): RequestHandlerInterface
    {
        $response = $response ?: $this->createDefaultResponse();
        return new class ($response) implements RequestHandlerInterface {
            private ResponseInterface $response;

            public function __construct(ResponseInterface $response)
            {
                $this->response = $response;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return $this->response;
            }
        };
    }

    public function createDefaultResponse(): ResponseInterface
    {
        $this->response = $this->createMock(ResponseInterface::class);
        return $this->response;
    }

    /**
     * @group http-interop
     */
    public function testNextImplementsRequestHandlerInterface(): void
    {
        $next = new Next($this->queue, $this->fallbackHandler);
        $this->assertInstanceOf(RequestHandlerInterface::class, $next);
    }

    public function testMiddlewareCallingNextWithRequestPassesRequestToNextMiddleware(): void
    {
        $request = $this->request->withUri(new Uri('http://example.com/foo/bar/baz'));
        self::assertInstanceOf(ServerRequestInterface::class, $request);
        $cannedRequest = clone $request;
        $cannedRequest = $cannedRequest->withMethod('POST');

        $middleware1 = new class ($cannedRequest) implements MiddlewareInterface
        {
            private ServerRequestInterface $cannedRequest;

            public function __construct(ServerRequestInterface $cannedRequest)
            {
                $this->cannedRequest = $cannedRequest;
            }

            public function process(ServerRequestInterface $req, RequestHandlerInterface $handler): ResponseInterface
            {
                return $handler->handle($this->cannedRequest);
            }
        };

        $middleware2 = new class ($cannedRequest) implements MiddlewareInterface
        {
            private ServerRequestInterface $cannedRequest;

            public function __construct(ServerRequestInterface $cannedRequest)
            {
                $this->cannedRequest = $cannedRequest;
            }

            public function process(ServerRequestInterface $req, RequestHandlerInterface $handler): ResponseInterface
            {
                Assert::assertEquals($this->cannedRequest->getMethod(), $req->getMethod());
                return new Response();
            }
        };

        $this->queue->enqueue($middleware1);
        $this->queue->enqueue($middleware2);

        $next     = new Next($this->queue, $this->fallbackHandler);
        $response = $next->handle($request);
        $this->assertNotSame($this->response, $response);
    }

    /**
     * @group http-interop
     */
    public function testNextDelegatesToFallbackHandlerWhenQueueIsEmpty(): void
    {
        $expectedResponse = $this->createMock(ResponseInterface::class);
        $fallbackHandler  = $this->createMock(RequestHandlerInterface::class);
        $fallbackHandler
            ->expects(self::once())
            ->method('handle')
            ->with($this->request)
            ->willReturn($expectedResponse);
        $next = new Next($this->queue, $fallbackHandler);
        $this->assertSame($expectedResponse, $next->handle($this->request));
    }

    /**
     * @group http-interop
     */
    public function testNextProcessesEnqueuedMiddleware(): void
    {
        $fallbackHandler = $this->createMock(RequestHandlerInterface::class);
        $fallbackHandler
            ->expects(self::never())
            ->method('handle');

        $response = $this->createMock(ResponseInterface::class);

        $middleware = $this->createMock(MiddlewareInterface::class);
        $middleware
            ->method('process')
            ->with($this->request)
            ->willReturn($response);

        $this->queue->enqueue($middleware);

        // Creating after middleware enqueued, as Next clones the queue during
        // instantiation.
        $next = new Next($this->queue, $fallbackHandler);

        $this->assertSame($response, $next->handle($this->request));
    }

    /**
     * @group http-interop
     */
    public function testMiddlewareReturningResponseShortCircuitsProcess(): void
    {
        $fallbackHandler = $this->createMock(RequestHandlerInterface::class);
        $fallbackHandler
            ->expects(self::never())
            ->method('handle');

        $response = $this->createMock(ResponseInterface::class);

        $route1 = $this->createMock(MiddlewareInterface::class);
        $route1
            ->method('process')
            ->with($this->request)
            ->willReturn($response);
        $this->queue->enqueue($route1);

        $route2 = $this->createMock(MiddlewareInterface::class);
        $route2
            ->expects(self::never())
            ->method('process');
        $this->queue->enqueue($route2);

        // Creating after middleware enqueued, as Next clones the queue during
        // instantiation.
        $next = new Next($this->queue, $fallbackHandler);

        $this->assertSame($response, $next->handle($this->request));
    }

    public function testNextHandlerCannotBeInvokedTwice(): void
    {
        $fallbackHandler = $this->createMock(RequestHandlerInterface::class);
        $fallbackHandler
            ->method('handle')
            ->willReturn(new Response());

        $this->queue->push(new DelegatingMiddleware());

        $next = new Next($this->queue, $fallbackHandler);
        $next->handle($this->request);

        $this->expectException(MiddlewarePipeNextHandlerAlreadyCalledException::class);
        $next->handle($this->request);
    }

    public function testSecondInvocationAttemptDoesNotInvokeFinalHandler(): void
    {
        $fallBackHandler = $this->createMock(RequestHandlerInterface::class);
        $fallBackHandler
            ->expects(self::once())
            ->method('handle')
            ->willReturn(new Response());

        $this->queue->push(new DelegatingMiddleware());

        $next = new Next($this->queue, $fallBackHandler);
        $next->handle($this->request);

        $this->expectException(MiddlewarePipeNextHandlerAlreadyCalledException::class);
        $next->handle($this->request);
    }

    public function testSecondInvocationAttemptDoesNotInvokeMiddleware(): void
    {
        $fallBackHandler = $this->createMock(RequestHandlerInterface::class);
        $fallBackHandler
            ->method('handle')
            ->willReturn(new Response());

        $middleware = $this->createMock(MiddlewareInterface::class);
        $middleware
            ->expects(self::once())
            ->method('process')
            ->willReturnCallback(
                static fn(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface => $handler->handle($request)
            );

        $this->queue->push($middleware);

        $next = new Next($this->queue, $fallBackHandler);
        $next->handle($this->request);

        $this->expectException(MiddlewarePipeNextHandlerAlreadyCalledException::class);
        $next->handle($this->request);
    }

    public function testShortCircuitingMiddlewareDoesNotEnableSecondInvocation(): void
    {
        $fallBackHandler = $this->createMock(RequestHandlerInterface::class);
        $fallBackHandler
            ->expects(self::never())
            ->method('handle');

        $this->queue->push(new ShortCircuitingMiddleware());

        // The middleware above shorcircuits (when handler is invoked first)
        // The middleware below still exists in the queue (when handler is invoked again)
        $this->queue->push(new DelegatingMiddleware());

        $next = new Next($this->queue, $fallBackHandler);
        $next->handle($this->request);

        $this->expectException(MiddlewarePipeNextHandlerAlreadyCalledException::class);
        $next->handle($this->request);
    }

    public function testSecondInvocationAttemptWithEmptyQueueDoesNotInvokeFinalHandler(): void
    {
        $fallBackHandler = $this->createMock(RequestHandlerInterface::class);
        $fallBackHandler
            ->expects(self::once())
            ->method('handle')
            ->willReturn(new Response());

        $next = new Next($this->queue, $fallBackHandler);

        $next->handle($this->request);

        $this->expectException(MiddlewarePipeNextHandlerAlreadyCalledException::class);
        $next->handle($this->request);
    }
}
