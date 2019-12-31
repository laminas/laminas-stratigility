<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Stratigility\Exception;

use RuntimeException;

/**
 * Exception thrown when Dispatch::process() is called with a non-interop
 * handler provided, and the request is not a server request type.
 */
class InvalidRequestTypeException extends RuntimeException
{
}
