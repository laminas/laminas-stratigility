<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Stratigility\Middleware\TestAsset;

use Psr\Http\Message\ServerRequestInterface;
use Webimpress\HttpMiddlewareCompatibility\HandlerInterface as RequestHandlerInterface;
use Webimpress\HttpMiddlewareCompatibility\MiddlewareInterface;
use Laminas\Diactoros\Response;

class XFoundMiddleware implements MiddlewareInterface
{
    /**
     * @return Response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        return (new Response())->withHeader('X-Found', 'true');
    }
}
