<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Stratigility\Exception;

use RuntimeException;

/**
 * Exception raised when a string $err is provided and raise throwables is enabled.
 *
 * @todo Remove for 2.0.0.
 */
class MiddlewareException extends RuntimeException
{
    /**
     * Create an instance based on an error value.
     *
     * @param mixed $err
     * @return self
     */
    public static function fromErrorValue($err)
    {
        if (is_object($err)) {
            return self::fromType(get_class($err));
        }

        if (is_array($err)) {
            return self::fromType(gettype($err));
        }

        if (is_string($err)) {
            throw new self($err);
        }

        return self::fromType(var_export($err, true));
    }

    /**
     * Create an instance using a templated error string.
     *
     * @param string $value
     * @return self
     */
    private static function fromType($value)
    {
        return new self(sprintf(
            'Middleware raised an error condition: %s',
            $value
        ));
    }
}
