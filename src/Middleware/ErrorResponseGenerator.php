<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Stratigility\Middleware;

use Exception;
use Laminas\Escaper\Escaper;
use Laminas\Stratigility\Utils;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final class ErrorResponseGenerator
{
    /**
     * @var bool
     */
    private $isDevelopmentMode;

    /**
     * @param bool $isDevelopmentMode
     */
    public function __construct($isDevelopmentMode = false)
    {
        $this->isDevelopmentMode = (bool) $isDevelopmentMode;
    }

    /**
     * Create/update the response representing the error.
     *
     * @param Throwable|Exception $e
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke($e, ServerRequestInterface $request, ResponseInterface $response)
    {
        $response = $response->withStatus(Utils::getStatusCode($e, $response));
        $body = $response->getBody();

        if ($this->isDevelopmentMode) {
            $escaper = new Escaper();
            $body->write($escaper->escapeHtml((string) $e));
            return $response;
        }

        $body->write($response->getReasonPhrase() ?: 'Unknown Error');
        return $response;
    }
}
