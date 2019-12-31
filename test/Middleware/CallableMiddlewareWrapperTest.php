<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Stratigility\Middleware;

use Closure;
use Laminas\Stratigility\Middleware\CallableMiddlewareWrapper;
use Laminas\Stratigility\Next;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Webimpress\HttpMiddlewareCompatibility\HandlerInterface as DelegateInterface;

use const Webimpress\HttpMiddlewareCompatibility\HANDLER_METHOD;

class CallableMiddlewareWrapperTest extends TestCase
{
    public function testWrapperDecoratesAndProxiesToCallableMiddleware()
    {
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $delegate = $this->prophesize(DelegateInterface::class)->reveal();
        $response = $this->prophesize(ResponseInterface::class)->reveal();

        $decorator = new CallableMiddlewareWrapper(
            function ($request, $response, $delegate) {
                return $response;
            },
            $response
        );

        $this->assertSame($response, $decorator->process($request, $delegate));
    }

    public function testWrapperDoesNotDecorateNextInstancesWhenProxying()
    {
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $response = $this->prophesize(ResponseInterface::class)->reveal();

        $delegate = $this->prophesize(Next::class)->reveal();
        $decorator = new CallableMiddlewareWrapper(
            function ($request, $response, $next) use ($delegate) {
                $this->assertSame($delegate, $next);
                return $response;
            },
            $response
        );

        $this->assertSame($response, $decorator->process($request, $delegate));
    }

    public function testWrapperDecoratesDelegatesNotExtendingNext()
    {
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $response = $this->prophesize(ResponseInterface::class)->reveal();

        $delegate = $this->prophesize(DelegateInterface::class)->reveal();
        $decorator = new CallableMiddlewareWrapper(
            function ($request, $response, $next) use ($delegate) {
                $this->assertNotSame($delegate, $next);
                $this->assertInstanceOf(Closure::class, $next);
                return $response;
            },
            $response
        );

        $this->assertSame($response, $decorator->process($request, $delegate));
    }

    public function testDecoratedDelegateWillBeInvokedWithOnlyRequest()
    {
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $response = $this->prophesize(ResponseInterface::class)->reveal();
        $expected = $this->prophesize(ResponseInterface::class)->reveal();

        $delegate = $this->prophesize(DelegateInterface::class);
        $delegate->{HANDLER_METHOD}($request)->willReturn($expected);

        $decorator = new CallableMiddlewareWrapper(
            function ($request, $response, $next) {
                return $next($request, $response);
            },
            $response
        );

        $this->assertSame($expected, $decorator->process($request, $delegate->reveal()));
    }
}
