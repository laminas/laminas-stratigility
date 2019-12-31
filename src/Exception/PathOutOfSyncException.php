<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Stratigility\Exception;

use RuntimeException;

class PathOutOfSyncException extends RuntimeException
{
    /**
     * @param string $pathPrefix
     * @param string $path
     * @return self
     */
    public static function forPath($pathPrefix, $path)
    {
        return new self(sprintf(
            'Layer path "%s" and request path "%s" are out of sync; cannot dispatch'
            . ' middleware layer',
            $pathPrefix,
            $path
        ));
    }
}
