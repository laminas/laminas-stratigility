<?php

declare(strict_types=1);

namespace Laminas\Stratigility;

use Laminas\Stratigility\Exception\MiddlewarePipeNextHandlerAlreadyCalledException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplQueue;

/**
 * Iterate a queue of middlewares and execute them.
 */
final class Next implements RequestHandlerInterface
{
    /** @var SplQueue<MiddlewareInterface>|null */
    private ?SplQueue $queue;

    /**
     * Clones the queue provided to allow re-use.
     *
     * @param SplQueue<MiddlewareInterface> $queue
     * @param RequestHandlerInterface $fallbackHandler Fallback handler to
     *     invoke when the queue is exhausted.
     */
    public function __construct(SplQueue $queue, private RequestHandlerInterface $fallbackHandler)
    {
        $this->queue = clone $queue;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->queue === null) {
            throw MiddlewarePipeNextHandlerAlreadyCalledException::create();
        }

        if ($this->queue->isEmpty()) {
            $this->queue = null;
            return $this->fallbackHandler->handle($request);
        }

        $middleware  = $this->queue->dequeue();
        $next        = clone $this; // deep clone is not used intentionally
        $this->queue = null; // mark queue as processed at this nesting level

        return $middleware->process($request, $next);
    }
}
