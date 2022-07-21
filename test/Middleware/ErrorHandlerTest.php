<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility\Middleware;

use Laminas\Escaper\Escaper;
use Laminas\Stratigility\Middleware\ErrorHandler;
use Laminas\Stratigility\Middleware\ErrorResponseGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionObject;
use RuntimeException;
use Throwable;

use function error_reporting;
use function trigger_error;

use const E_USER_DEPRECATED;

class ErrorHandlerTest extends TestCase
{
    /** @var MockObject&StreamInterface */
    private $body;

    /** @var MockObject&RequestHandlerInterface */
    private $handler;

    /** @var MockObject&ServerRequestInterface */
    private $request;

    /** @var MockObject&ResponseInterface */
    private $response;

    /** @var callable():ResponseInterface */
    private $responseFactory;

    private int $errorReporting;

    protected function setUp(): void
    {
        $this->response        = $this->createMock(ResponseInterface::class);
        $response              = $this->response;
        $this->responseFactory = static fn(): ResponseInterface => $response;

        $this->request        = $this->createMock(ServerRequestInterface::class);
        $this->body           = $this->createMock(StreamInterface::class);
        $this->handler        = $this->createMock(RequestHandlerInterface::class);
        $this->errorReporting = error_reporting();
    }

    protected function tearDown(): void
    {
        error_reporting($this->errorReporting);
    }

    public function createMiddleware(bool $isDevelopmentMode = false): ErrorHandler
    {
        $generator = new ErrorResponseGenerator($isDevelopmentMode);
        return new ErrorHandler($this->responseFactory, $generator);
    }

    public function testReturnsResponseFromHandlerWhenNoProblemsOccur(): void
    {
        $expectedResponse = $this->createMock(ResponseInterface::class);

        $this->handler
            ->expects(self::once())
            ->method('handle')
            ->with($this->request)
            ->willReturn($expectedResponse);

        $this->response
            ->expects(self::never())
            ->method('withStatus');

        $middleware = $this->createMiddleware();
        $result     = $middleware->process($this->request, $this->handler);

        $this->assertSame($expectedResponse, $result);
    }

    public function testReturnsErrorResponseIfHandlerRaisesAnErrorInTheErrorMask(): void
    {
        error_reporting(E_USER_DEPRECATED);
        $this->handler
            ->method('handle')
            ->with($this->request)
            ->will(self::returnCallback(static function () {
                trigger_error('Deprecated', E_USER_DEPRECATED);
            }));

        $this->body
            ->expects(self::once())
            ->method('write')
            ->with('Unknown Error')
            ->willReturnSelf();

        $this->response
            ->method('getStatusCode')
            ->willReturn(200);
        $this->response
            ->method('withStatus')
            ->with(500)
            ->willReturnSelf();

        $this->response
            ->method('getReasonPhrase')
            ->willReturn('');

        $this->response
            ->method('getBody')
            ->willReturn($this->body);

        $middleware = $this->createMiddleware();
        $result     = $middleware->process($this->request, $this->handler);

        $this->assertSame($this->response, $result);
    }

    public function testReturnsResponseFromHandlerWhenErrorRaisedIsNotInTheErrorMask(): void
    {
        $originalMask = error_reporting();
        error_reporting($originalMask & ~E_USER_DEPRECATED);

        $expectedResponse = $this->createMock(ResponseInterface::class);
        $this->handler
            ->method('handle')
            ->with($this->request)
            ->will(self::returnCallback(static function () use ($expectedResponse): ResponseInterface {
                trigger_error('Deprecated', E_USER_DEPRECATED);
                return $expectedResponse;
            }));

        $this->body
            ->expects(self::never())
            ->method('write');

        $this->response
            ->expects(self::never())
            ->method('getStatusCode');
        $this->response
            ->expects(self::never())
            ->method('withStatus');

        $middleware = $this->createMiddleware();
        $result     = $middleware->process($this->request, $this->handler);

        $this->assertSame($expectedResponse, $result);
    }

    public function testReturnsErrorResponseIfHandlerRaisesAnException(): void
    {
        $this->handler
            ->method('handle')
            ->with($this->request)
            ->willThrowException(new RuntimeException('Exception raised', 503));

        $this->body
            ->expects(self::once())
            ->method('write')
            ->with('Unknown Error')
            ->willReturnSelf();

        $this->response
            ->method('getStatusCode')
            ->willReturn(200);
        $this->response
            ->method('withStatus')
            ->with(503)
            ->willReturnSelf();

        $this->response
            ->method('getReasonPhrase')
            ->willReturn('');

        $this->response
            ->method('getBody')
            ->willReturn($this->body);

        $middleware = $this->createMiddleware();
        $result     = $middleware->process($this->request, $this->handler);

        $this->assertSame($this->response, $result);
    }

    public function testResponseErrorMessageIncludesStackTraceIfDevelopmentModeIsEnabled(): void
    {
        $exception = new RuntimeException('Exception raised', 503);
        $this->handler
            ->method('handle')
            ->with($this->request)
            ->willThrowException($exception);

        $this->body
            ->expects(self::once())
            ->method('write')
            ->with(
                (new Escaper())
                    ->escapeHtml((string) $exception)
            )
            ->willReturnSelf();
        $this->response
            ->method('getStatusCode')
            ->willReturn(200);
        $this->response
            ->method('withStatus')
            ->with(503)
            ->willReturnSelf();
        $this->response
            ->method('getReasonPhrase')
            ->willReturn('');
        $this->response
            ->method('getBody')
            ->willReturn($this->body);

        $middleware = $this->createMiddleware(true);
        $result     = $middleware->process($this->request, $this->handler);

        $this->assertSame($this->response, $result);
    }

    public function testErrorHandlingTriggersListeners(): void
    {
        $exception = new RuntimeException('Exception raised', 503);
        $this->handler
            ->method('handle')
            ->with($this->request)
            ->willThrowException($exception);

        $this->body
            ->expects(self::once())
            ->method('write')
            ->with('Unknown Error')
            ->willReturnSelf();
        $this->response
            ->method('getStatusCode')
            ->willReturn(200);

        $this->response
            ->method('withStatus')
            ->with(503)
            ->willReturnSelf();
        $this->response
            ->method('getReasonPhrase')
            ->willReturn('');

        $this->response
            ->method('getBody')
            ->willReturn($this->body);

        $listener  = function (
            Throwable $error,
            ServerRequestInterface $request,
            ResponseInterface $response
        ) use ($exception): void {
            $this->assertSame($exception, $error, 'Listener did not receive same exception as was raised');
            $this->assertSame($this->request, $request, 'Listener did not receive same request');
            $this->assertSame($this->response, $response, 'Listener did not receive same response');
        };
        $listener2 = clone $listener;

        $middleware = $this->createMiddleware();
        $middleware->attachListener($listener);
        $middleware->attachListener($listener2);

        $result = $middleware->process($this->request, $this->handler);

        $this->assertSame($this->response, $result);
    }

    public function testCanProvideAlternateErrorResponseGenerator(): void
    {
        $generator = static function (
            Throwable $e,
            ServerRequestInterface $request,
            ResponseInterface $response
        ): ResponseInterface {
            $response = $response->withStatus(400);
            $response->getBody()->write('The client messed up');
            return $response;
        };

        $this->handler
            ->method('handle')
            ->with($this->request)
            ->willThrowException(new RuntimeException('Exception raised', 503));

        $this->response
            ->method('withStatus')
            ->with(400)
            ->willReturnSelf();

        $this->response
            ->method('getBody')
            ->willReturn($this->body);

        $this->body
            ->expects(self::once())
            ->method('write')
            ->with('The client messed up')
            ->willReturnSelf();

        $middleware = new ErrorHandler($this->responseFactory, $generator);
        $result     = $middleware->process($this->request, $this->handler);

        $this->assertSame($this->response, $result);
    }

    public function testTheSameListenerIsAttachedOnlyOnce(): void
    {
        $middleware = $this->createMiddleware();
        $listener   = static function (): void {
        };

        $middleware->attachListener($listener);
        $middleware->attachListener($listener);

        $ref  = new ReflectionObject($middleware);
        $prop = $ref->getProperty('listeners');
        $prop->setAccessible(true);

        $listeners = $prop->getValue($middleware);

        self::assertCount(1, $listeners);
    }
}
