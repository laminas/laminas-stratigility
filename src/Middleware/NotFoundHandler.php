<?php

declare(strict_types=1);

namespace Laminas\Stratigility\Middleware;

use Laminas\Stratigility\Handler\NotFoundHandler as NotFoundRequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @deprecated Will be removed in v4 in favor of {@see \Laminas\Stratigility\Handler\NotFoundHandler}
 */
final class NotFoundHandler implements MiddlewareInterface
{
    private NotFoundRequestHandler $notFoundHandler;

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
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->notFoundHandler->handle($request);
    }
}
