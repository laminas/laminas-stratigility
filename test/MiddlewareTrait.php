<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility;

use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

trait MiddlewareTrait
{
    private function getNotCalledMiddleware(): MiddlewareInterface
    {
        $middleware = $this->prophesize(MiddlewareInterface::class);
        $middleware->process(Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        return $middleware->reveal();
    }

    private function getPassToHandlerMiddleware(): MiddlewareInterface
    {
        $middleware = $this->prophesize(MiddlewareInterface::class);
        $middleware->process(Argument::any(), Argument::any())
            ->will(function (array $args) {
                return $args[1]->handle($args[0]);
            })
            ->shouldBeCalledTimes(1);

        return $middleware->reveal();
    }

    private function getMiddlewareWhichReturnsResponse(ResponseInterface $response): MiddlewareInterface
    {
        $middleware = $this->prophesize(MiddlewareInterface::class);
        $middleware->process(Argument::any(), Argument::any())
            ->willReturn($response)
            ->shouldBeCalledTimes(1);

        return $middleware->reveal();
    }
}
