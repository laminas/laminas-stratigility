<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Stratigility;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class NoopFinalHandler
{
    /**
     * Final handler for all requests.
     *
     * This handler should only ever be invoked if Next exhausts its stack.
     *
     * When that happens, it returns the response provided during invocation.
     *
     * @param ServerRequestInterface $request Request instance.
     * @param ResponseInterface $response Response instance.
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        return $response;
    }
}
