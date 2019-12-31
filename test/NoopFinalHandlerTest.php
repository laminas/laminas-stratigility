<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Stratigility;

use Laminas\Stratigility\NoopFinalHandler;
use PHPUnit_Framework_TestCase as TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class NoopFinalHandlerTest extends TestCase
{
    public function testHandlerReturnsResponseProvidedAtInvocation()
    {
        $handler  = new NoopFinalHandler();
        $request  = $this->prophesize(ServerRequestInterface::class)->reveal();
        $response = $this->prophesize(ResponseInterface::class)->reveal();

        $this->assertSame($response, $handler($request, $response));
    }
}
