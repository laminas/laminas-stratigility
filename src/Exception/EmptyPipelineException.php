<?php

declare(strict_types=1);

namespace Laminas\Stratigility\Exception;

use OutOfBoundsException;

use function sprintf;

/**
 * Exception thrown when a MiddlewarePipe attempts to handle() a request,
 * but no middleware are composed in the instance.
 */
class EmptyPipelineException extends OutOfBoundsException implements ExceptionInterface
{
    public static function forClass(string $className) : self
    {
        return new self(sprintf(
            '%s cannot handle request; no middleware available to process the request',
            $className
        ));
    }
}
