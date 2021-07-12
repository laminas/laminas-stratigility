<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest as Request;
use Laminas\Stratigility\Exception;
use Laminas\Stratigility\MiddlewarePipe;
use Laminas\Stratigility\MiddlewarePipeInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionObject;

use function get_class;
use function sort;
use function spl_object_hash;
use function strpos;
use function var_export;

class MiddlewarePipeTest extends TestCase
{
    use MiddlewareTrait;

    /** @var Request */
    private $request;

    /** @var MiddlewarePipe */
    private $pipeline;

    protected function setUp(): void
    {
        $this->request  = new Request([], [], 'http://example.com/', 'GET', 'php://memory');
        $this->pipeline = new MiddlewarePipe();
    }

    private function createFinalHandler(): RequestHandlerInterface
    {
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler
            ->method('handle')
            ->willReturn(new Response());

        return $handler;
    }

    /**
     * @group http-interop
     */
    public function testCanPipeInteropMiddleware(): void
    {
        $handler = $this->createMock(RequestHandlerInterface::class);

        $response   = $this->createMock(ResponseInterface::class);
        $middleware = $this->createMock(MiddlewareInterface::class);
        $middleware
            ->method('process')
            ->with(
                self::callback(
                /** @psalm-suppress MissingClosureParamType */
                    static function ($argument1): bool {
                        self::assertInstanceOf(ServerRequestInterface::class, $argument1);
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
            ->willReturn($response);

        $pipeline = new MiddlewarePipe();
        $pipeline->pipe($middleware);

        $this->assertSame($response, $pipeline->process($this->request, $handler));
    }

    public function testProcessInvokesUntilFirstHandlerThatDoesNotCallNext(): void
    {
        $this->pipeline->pipe(new class () implements MiddlewareInterface
        {
            public function process(ServerRequestInterface $req, RequestHandlerInterface $handler): ResponseInterface
            {
                $res = $handler->handle($req);
                $res->getBody()->write("First\n");

                return $res;
            }
        });
        $this->pipeline->pipe(new class () implements MiddlewareInterface
        {
            public function process(ServerRequestInterface $req, RequestHandlerInterface $handler): ResponseInterface
            {
                $res = $handler->handle($req);
                $res->getBody()->write("Second\n");

                return $res;
            }
        });

        $response = new Response();
        $response->getBody()->write("Third\n");
        $this->pipeline->pipe($this->getMiddlewareWhichReturnsResponse($response));

        $this->pipeline->pipe($this->getNotCalledMiddleware());

        $request  = new Request([], [], 'http://local.example.com/foo', 'GET', 'php://memory');
        $response = $this->pipeline->process($request, $this->createFinalHandler());
        $body     = (string) $response->getBody();
        $this->assertStringContainsString('First', $body);
        $this->assertStringContainsString('Second', $body);
        $this->assertStringContainsString('Third', $body);
    }

    public function testInvokesHandlerWhenQueueIsExhausted(): void
    {
        $expected = $this->createMock(ResponseInterface::class);

        $this->pipeline->pipe($this->getPassToHandlerMiddleware());
        $this->pipeline->pipe($this->getPassToHandlerMiddleware());
        $this->pipeline->pipe($this->getPassToHandlerMiddleware());

        $request = new Request([], [], 'http://local.example.com/foo', 'GET', 'php://memory');

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')
        ->with($request)
            ->willReturn($expected);

        $result = $this->pipeline->process($request, $handler);

        $this->assertSame($expected, $result);
    }

    public function testReturnsResponseReturnedByQueue(): void
    {
        $return = new Response();

        $this->pipeline->pipe($this->getPassToHandlerMiddleware());
        $this->pipeline->pipe($this->getPassToHandlerMiddleware());
        $this->pipeline->pipe($this->getMiddlewareWhichReturnsResponse($return));

        $this->pipeline->pipe($this->getNotCalledMiddleware());

        $request = new Request([], [], 'http://local.example.com/foo', 'GET', 'php://memory');
        $result  = $this->pipeline->process($request, $this->createFinalHandler());
        $this->assertSame($return, $result, var_export([
            spl_object_hash($return) => get_class($return),
            spl_object_hash($result) => get_class($result),
        ], true));
    }

    public function testHandleRaisesExceptionIfQueueIsEmpty(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);

        $this->expectException(Exception\EmptyPipelineException::class);

        $this->pipeline->handle($request);
    }

    public function testHandleProcessesEnqueuedMiddleware(): void
    {
        $response    = $this->createMock(ResponseInterface::class);
        $middleware1 = $this->createMock(MiddlewareInterface::class);
        $middleware1
            ->method('process')
            ->with(
                $this->request
            )
            ->willReturnCallback(
                static function (ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
                    return $handler->handle($request);
                }
            );
        $middleware2 = $this->createMock(MiddlewareInterface::class);
        $middleware2
            ->method('process')
        ->with($this->request)
            ->willReturn($response);

        $pipeline = new MiddlewarePipe();
        $pipeline->pipe($middleware1);
        $pipeline->pipe($middleware2);

        $this->assertSame($response, $pipeline->handle($this->request));
    }

    public function testMiddlewarePipeOnlyImplementsMiddlewarePipeInterfaceApi(): void
    {
        $pipeline = new MiddlewarePipe();

        $r       = new ReflectionObject($pipeline);
        $methods = $r->getMethods(ReflectionMethod::IS_PUBLIC);
        $actual  = [];
        foreach ($methods as $method) {
            if (strpos($method->getName(), '__') !== 0) {
                $actual[] = $method->getName();
            }
        }
        sort($actual);

        $interfaceReflection = new ReflectionClass(MiddlewarePipeInterface::class);
        $interfaceMethods    = $interfaceReflection->getMethods(ReflectionMethod::IS_PUBLIC);
        $expected            = [];
        foreach ($interfaceMethods as $method) {
            $expected[] = $method->getName();
        }
        sort($expected);

        self::assertTrue($r->isFinal());
        self::assertEquals($expected, $actual);
        self::assertInstanceOf(MiddlewarePipeInterface::class, $pipeline);
    }
}
