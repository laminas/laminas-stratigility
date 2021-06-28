<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 */

declare(strict_types=1);

namespace Laminas\Stratigility;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface MiddlewarePipeInterface extends MiddlewareInterface, RequestHandlerInterface
{
    public function pipe(MiddlewareInterface $middleware): void;
}
