<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Stratigility\Middleware;

use Generator;
use Laminas\Stratigility\Middleware\HostMiddlewareDecorator;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function Laminas\Stratigility\host;

class HostMiddlewareDecoratorTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var UriInterface|ObjectProphecy
     */
    private $uri;

    /**
     * @var ServerRequestInterface|ObjectProphecy
     */
    private $request;

    /**
     * @var ResponseInterface|ObjectProphecy
     */
    private $response;

    /**
     * @var RequestHandlerInterface|ObjectProphecy
     */
    private $handler;

    /**
     * @var MiddlewareInterface|ObjectProphecy
     */
    private $toDecorate;

    protected function setUp() : void
    {
        $this->uri = $this->prophesize(UriInterface::class);
        $this->request = $this->prophesize(ServerRequestInterface::class);
        $this->response = $this->prophesize(ResponseInterface::class);
        $this->handler = $this->prophesize(RequestHandlerInterface::class);
        $this->toDecorate = $this->prophesize(MiddlewareInterface::class);
    }

    public function testImplementsMiddlewareInterface()
    {
        $middleware = new HostMiddlewareDecorator('host.test', $this->toDecorate->reveal());
        self::assertInstanceOf(MiddlewareInterface::class, $middleware);
    }

    public function testDelegatesOriginalRequestToHandlerIfRequestHostDoesNotMatchDecoratorHostName()
    {
        $this->uri->getHost()->willReturn('host.foo');
        $this->request->getUri()->will([$this->uri, 'reveal']);
        $this->handler
            ->handle(Argument::that([$this->request, 'reveal']))
            ->will([$this->response, 'reveal']);

        $this->toDecorate->process(Argument::any())->shouldNotBeCalled();

        $decorator = new HostMiddlewareDecorator('host.bar', $this->toDecorate->reveal());
        $decorator->process($this->request->reveal(), $this->handler->reveal());
    }

    public function matchingHost() : Generator
    {
        yield ['host.foo', 'host.foo'];
        yield ['host.foo', 'HOST.FOO'];
        yield ['host.foo', 'hOsT.fOO'];
    }

    /**
     * @dataProvider matchingHost
     */
    public function testDelegatesOriginalRequestToDecoratedMiddleware(string $requestHost, string $decoratorHost)
    {
        $this->uri->getHost()->willReturn($requestHost);
        $this->request->getUri()->will([$this->uri, 'reveal']);
        $this->handler->handle(Argument::any())->shouldNotBeCalled();
        $this->toDecorate
            ->process(
                Argument::that([$this->request, 'reveal']),
                Argument::that([$this->handler, 'reveal'])
            )
            ->will([$this->response, 'reveal'])
            ->shouldBeCalledTimes(1);

        $decorator = new HostMiddlewareDecorator($decoratorHost, $this->toDecorate->reveal());
        $decorator->process($this->request->reveal(), $this->handler->reveal());
    }

    public function testHostFunction()
    {
        $toDecorate = $this->toDecorate->reveal();

        $middleware = host('foo.bar', $toDecorate);
        self::assertInstanceOf(HostMiddlewareDecorator::class, $middleware);
        self::assertEquals(new HostMiddlewareDecorator('foo.bar', $toDecorate), $middleware);
    }
}
