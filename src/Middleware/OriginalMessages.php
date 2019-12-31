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

/**
 * Inject attributes containing the original request and URI instances.
 *
 * This middleware will add request attributes as follows:
 *
 * - "originalRequest", representing the request provided to this middleware.
 * - "originalUri", representing the URI composed by the request provided to
 *   this middleware.
 *
 * These can then be reference later, for tasks such as:
 *
 * - Determining the base path when generating a URI (as layers may receive
 *   URIs stripping path segments).
 * - Determining if changes to the response have occurred.
 * - Providing prototypes for factories.
 */
final class OriginalMessages implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $request = $request
            ->withAttribute('originalUri', $request->getUri())
            ->withAttribute('originalRequest', $request);

        return $handler->handle($request);
    }
}
