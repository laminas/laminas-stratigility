<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace LaminasTest\Stratigility;

use Interop\Http\Server\MiddlewareInterface;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;

trait MiddlewareTrait
{
    private function getNotCalledMiddleware() : MiddlewareInterface
    {
        $middleware = $this->prophesize(MiddlewareInterface::class);
        $middleware->process(Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        return $middleware->reveal();
    }

    private function getPassToHandlerMiddleware() : MiddlewareInterface
    {
        $middleware = $this->prophesize(MiddlewareInterface::class);
        $middleware->process(Argument::any(), Argument::any())
            ->will(function (array $args) {
                return $args[1]->handle($args[0]);
            })
            ->shouldBeCalledTimes(1);

        return $middleware->reveal();
    }

    private function getMiddlewareWhichReturnsResponse(ResponseInterface $response) : MiddlewareInterface
    {
        $middleware = $this->prophesize(MiddlewareInterface::class);
        $middleware->process(Argument::any(), Argument::any())
            ->willReturn($response)
            ->shouldBeCalledTimes(1);

        return $middleware->reveal();
    }
}
