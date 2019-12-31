<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Stratigility\Middleware;

use Interop\Http\Middleware\DelegateInterface;
use Laminas\Stratigility\Middleware\CallableInteropMiddlewareWrapper;
use PHPUnit_Framework_TestCase as TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CallableInteropMiddlewareWrapperTest extends TestCase
{
    public function testWrapperDecoratesAndProxiesToCallableInteropMiddleware()
    {
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $delegate = $this->prophesize(DelegateInterface::class)->reveal();
        $response = $this->prophesize(ResponseInterface::class)->reveal();

        $decorator = new CallableInteropMiddlewareWrapper(
            function ($request, DelegateInterface $delegate) use ($response) {
                return $response;
            }
        );

        $this->assertSame($response, $decorator->process($request, $delegate));
    }
}
