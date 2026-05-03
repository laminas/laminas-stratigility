<?php

declare(strict_types=1);

namespace Laminas\Stratigility\Handler;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function sprintf;

final class NotFoundHandler implements RequestHandlerInterface
{
    public function __construct(private readonly ResponseFactoryInterface $responseFactory)
    {
    }

    /**
     * Creates and returns a 404 response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(StatusCode::STATUS_NOT_FOUND);

        $response->getBody()->write(sprintf(
            'Cannot %s %s',
            $request->getMethod(),
            (string) $request->getUri()
        ));

        return $response;
    }
}
