<?php

declare(strict_types=1);

namespace Laminas\Stratigility;

use IteratorAggregate;
use Psr\Http\Server\MiddlewareInterface;

/**
 * Implementors of this interface expose composed middleware as an iterable.
 *
 * This is useful for inspecting the middleware that is configured to run within a pipeline. Iterating over the pipeline
 * should *not* cause side effects such as de-queueing any composed middleware.
 *
 * @extends IteratorAggregate<int, MiddlewareInterface>
 */
interface IterableMiddlewarePipeInterface extends MiddlewarePipeInterface, IteratorAggregate
{
}
