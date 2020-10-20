<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Stratigility\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Stratigility\Handler\NotFoundHandler as NotFoundRequestHandler;

/**
 * @deprecated Will be removed in v4 in favor of {@see \Laminas\Stratigility\Handler\NotFoundHandler}
 */
final class NotFoundHandler implements MiddlewareInterface
{

    /**
     * @var NotFoundRequestHandler
     */
    private $notFoundHandler;

    /**
     * @param callable $responseFactory A factory capable of returning an
     *     empty ResponseInterface instance to update and return when returning
     *     an 404 response.
     */
    public function __construct(callable $responseFactory)
    {
        $this->notFoundHandler = new NotFoundRequestHandler($responseFactory);
    }

    /**
     * Uses the {@see \Laminas\Stratigility\Handler\NotFoundHandler} to create a 404 response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        return $this->notFoundHandler->handle($request);
    }
}
