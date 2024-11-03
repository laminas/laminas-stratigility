<?php

declare(strict_types=1);

namespace Laminas\Stratigility\Middleware;

use Laminas\Escaper\Escaper;
use Laminas\Stratigility\Utils;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final class ErrorResponseGenerator
{
    public function __construct(private readonly bool $isDevelopmentMode = false)
    {
    }

    /**
     * Create/update the response representing the error.
     */
    public function __invoke(
        Throwable $e,
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $response = $response->withStatus(Utils::getStatusCode($e, $response));
        $body     = $response->getBody();

        if ($this->isDevelopmentMode) {
            $escaper = new Escaper();
            $body->write($escaper->escapeHtml((string) $e));
            return $response;
        }

        $body->write($response->getReasonPhrase() ?: 'Unknown Error');
        return $response;
    }
}
