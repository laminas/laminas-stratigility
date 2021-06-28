<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility\TestAsset;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class ErrorHandler
{
    /**
     * @param Throwable $err
     * @param ServerRequestInterface $req
     * @param ResponseInterface $res
     * @param callable $next
     */
    public function handle($err, $req, $res, $next): void
    {
    }
}
