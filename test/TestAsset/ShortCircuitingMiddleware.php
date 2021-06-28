<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 */

declare(strict_types=1);

namespace LaminasTest\Stratigility\TestAsset;

use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ShortCircuitingMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $req, RequestHandlerInterface $handler): ResponseInterface
    {
        return new Response();
    }
}
