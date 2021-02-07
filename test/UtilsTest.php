<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Stratigility;

use Laminas\Diactoros\Response\TextResponse;
use Laminas\Stratigility\Utils;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
    public function testGetStatusCodeNotFooledBySneakyStringsWithLeadingDigits()
    {
        // PDO can throw exceptions with codes like this:
        $naughtyCode = '42S02';

        $exception = new \Exception();
        $reflection = new \ReflectionClass($exception);
        $code = $reflection->getProperty('code');
        $code->setAccessible(true);
        $code->setValue($exception, $naughtyCode);

        $actual = Utils::getStatusCode($exception, new TextResponse('I am a teapot.', 418));

        Assert::assertEquals(418, $actual);
    }
}
