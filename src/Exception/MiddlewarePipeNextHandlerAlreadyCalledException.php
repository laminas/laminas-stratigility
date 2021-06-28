<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 */

declare(strict_types=1);

namespace Laminas\Stratigility\Exception;

use DomainException;

class MiddlewarePipeNextHandlerAlreadyCalledException extends DomainException implements ExceptionInterface
{
    public static function create(): self
    {
        return new self('Cannot invoke pipeline handler $handler->handle() more than once');
    }
}
