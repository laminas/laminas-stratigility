<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Stratigility\Exception;

use RuntimeException;

/**
 * Exception thrown when the Dispatch::process is called and needs to execute
 * a non-interop middleware, but no response prototype was provided to the
 * instance.
 */
class MissingResponsePrototypeException extends RuntimeException
{
}
