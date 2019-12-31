<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Stratigility;

use Laminas\Stratigility\Exception\MiddlewarePipeNextHandlerAlreadyCalledException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplQueue;

/**
 * Iterate a queue of middlewares and execute them.
 */
final class Next implements RequestHandlerInterface
{
    /**
     * @var RequestHandlerInterface
     */
    private $fallbackHandler;

    /**
     * @var null|SplQueue
     */
    private $queue;

    /**
     * Clones the queue provided to allow re-use.
     *
     * @param RequestHandlerInterface $fallbackHandler Fallback handler to
     *     invoke when the queue is exhausted.
     */
    public function __construct(SplQueue $queue, RequestHandlerInterface $fallbackHandler)
    {
        $this->queue           = clone $queue;
        $this->fallbackHandler = $fallbackHandler;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        if ($this->queue === null) {
            throw MiddlewarePipeNextHandlerAlreadyCalledException::create();
        }

        if ($this->queue->isEmpty()) {
            $this->queue = null;
            return $this->fallbackHandler->handle($request);
        }

        $middleware = $this->queue->dequeue();
        $next = clone $this; // deep clone is not used intentionally
        $this->queue = null; // mark queue as processed at this nesting level

        return $middleware->process($request, $next);
    }
}
