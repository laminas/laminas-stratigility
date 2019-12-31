<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Stratigility\Exception;

use InvalidArgumentException;

class InvalidMiddlewareException extends InvalidArgumentException
{
    /**
     * Create and return an InvalidArgumentException detailing the invalid middleware type.
     *
     * @param mixed $value
     * @return InvalidArgumentException
     */
    public static function fromValue($value)
    {
        $received = gettype($value);

        if (is_object($value)) {
            $received = get_class($value);
        }

        return new self(
            sprintf(
                'Middleware must be callable, %s found',
                $received
            )
        );
    }
}
