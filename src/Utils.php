<?php // phpcs:disable WebimpressCodingStandard.NamingConventions.AbstractClass.Prefix


declare(strict_types=1);

namespace Laminas\Stratigility;

use Psr\Http\Message\ResponseInterface;
use Throwable;

use function is_int;

/**
 * Utility methods
 */
abstract class Utils
{
    /**
     * Determine status code from an error and/or response.
     *
     * If the error is an exception with a code between 400 and 599, returns
     * the exception code.
     *
     * Otherwise, retrieves the code from the response; if not present, or
     * less than 400 or greater than 599, returns 500; otherwise, returns it.
     */
    public static function getStatusCode(Throwable $error, ResponseInterface $response): int
    {
        $errorCode = $error->getCode();
        if (is_int($errorCode) && $errorCode >= 400 && $errorCode < 600) {
            return $errorCode;
        }

        $status = $response->getStatusCode();
        if (! $status || $status < 400 || $status >= 600) {
            $status = 500;
        }
        return $status;
    }
}
