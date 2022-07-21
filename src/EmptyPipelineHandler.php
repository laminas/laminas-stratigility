<?php

declare(strict_types=1);

namespace Laminas\Stratigility;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class EmptyPipelineHandler implements RequestHandlerInterface
{
    private string $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        throw Exception\EmptyPipelineException::forClass($this->className);
    }
}
