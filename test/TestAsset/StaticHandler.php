<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility\TestAsset;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class StaticHandler
{
    /**
     * @param ServerRequestInterface $req
     * @param ResponseInterface $res
     * @param callable $next
     */
    public static function handle($req, $res, $next): void
    {
    }
}
