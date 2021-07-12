<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility\Middleware;

use Laminas\Stratigility\Middleware\OriginalMessages;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OriginalMessagesTest extends TestCase
{
    /** @var MockObject&UriInterface */
    private $uri;

    /** @var MockObject&ServerRequestInterface */
    private $request;

    protected function setUp(): void
    {
        $this->uri     = $this->createMock(UriInterface::class);
        $this->request = $this->createMock(ServerRequestInterface::class);
    }

    public function testNextReceivesRequestWithNewAttributes(): void
    {
        $middleware = new OriginalMessages();
        $expected   = $this->createMock(ResponseInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler
            ->method('handle')
            ->with($this->request)
            ->willReturn($expected);

        $this->request
            ->method('getUri')
            ->willReturn($this->uri);

        $this->request
            ->method('withAttribute')
            ->withConsecutive(
                ['originalUri', $this->uri],
                ['originalRequest', $this->request]
            )
            ->willReturnSelf();

        $response = $middleware->process($this->request, $handler);

        $this->assertSame($expected, $response);
    }
}
