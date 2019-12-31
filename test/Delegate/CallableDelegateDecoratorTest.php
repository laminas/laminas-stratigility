<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Stratigility\Delegate;

use Laminas\Stratigility\Delegate\CallableDelegateDecorator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CallableDelegateDecoratorTest extends TestCase
{
    public function setUp()
    {
        $this->response = $this->prophesize(ResponseInterface::class)->reveal();
    }

    public function testProcessWillProxyToComposedDelegate()
    {
        $originalRequest = $this->prophesize(ServerRequestInterface::class)->reveal();

        $delegate = function ($request, $response) use ($originalRequest) {
            Assert::assertSame($originalRequest, $request);
            Assert::assertSame($this->response, $response);
            return $response;
        };

        $decorator = new CallableDelegateDecorator($delegate, $this->response);

        $this->assertSame($this->response, $decorator->process($originalRequest));
    }

    public function testHandleWillProxyToComposedDelegate()
    {
        $originalRequest = $this->prophesize(ServerRequestInterface::class)->reveal();

        $delegate = function ($request, $response) use ($originalRequest) {
            Assert::assertSame($originalRequest, $request);
            Assert::assertSame($this->response, $response);
            return $response;
        };

        $decorator = new CallableDelegateDecorator($delegate, $this->response);

        $this->assertSame($this->response, $decorator->handle($originalRequest));
    }

    public function testNextWillProxyToComposedDelegateUsingNonServerRequest()
    {
        $originalRequest = $this->prophesize(RequestInterface::class)->reveal();

        $delegate = function ($request, $response) use ($originalRequest) {
            Assert::assertSame($originalRequest, $request);
            Assert::assertSame($this->response, $response);
            return $response;
        };

        $decorator = new CallableDelegateDecorator($delegate, $this->response);

        $this->assertSame($this->response, $decorator->next($originalRequest));
    }

    public function testNextWillProxyToComposedDelegateUsingServerRequest()
    {
        $originalRequest = $this->prophesize(ServerRequestInterface::class)->reveal();

        $delegate = function ($request, $response) use ($originalRequest) {
            Assert::assertSame($originalRequest, $request);
            Assert::assertSame($this->response, $response);
            return $response;
        };

        $decorator = new CallableDelegateDecorator($delegate, $this->response);

        $this->assertSame($this->response, $decorator->next($originalRequest));
    }
}
