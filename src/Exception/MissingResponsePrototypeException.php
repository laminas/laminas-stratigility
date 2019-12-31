<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Stratigility\Exception;

use Laminas\Stratigility\Middleware\DoublePassMiddlewareDecorator;
use UnexpectedValueException;

use function sprintf;

/**
 * Exception thrown by the DoublePassMiddlewareDecorator when no response
 * prototype is provided, and Diactoros is not available to create a default.
 */
class MissingResponsePrototypeException extends UnexpectedValueException implements ExceptionInterface
{
    public static function create() : self
    {
        return new self(sprintf(
            'Unable to create a %s instance; no response prototype provided,'
            . ' and laminas/laminas-diactoros is not installed',
            DoublePassMiddlewareDecorator::class
        ));
    }
}
