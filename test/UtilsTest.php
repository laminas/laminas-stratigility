<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 */

declare(strict_types=1);

namespace LaminasTest\Stratigility;

use Exception;
use Laminas\Diactoros\Response\TextResponse;
use Laminas\Stratigility\Utils;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class UtilsTest extends TestCase
{
    public function testGetStatusCodeNotFooledBySneakyStringsWithLeadingDigits(): void
    {
        // PDO can throw exceptions with codes like this:
        $naughtyCode = '42S02';

        $exception  = new Exception();
        $reflection = new ReflectionClass($exception);
        $code       = $reflection->getProperty('code');
        $code->setAccessible(true);
        $code->setValue($exception, $naughtyCode);

        $actual = Utils::getStatusCode($exception, new TextResponse('I am a teapot.', 418));

        Assert::assertEquals(418, $actual);
    }
}
