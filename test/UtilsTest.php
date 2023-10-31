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
use Psr\Http\Message\ResponseInterface;
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

        $code->setValue($exception, $naughtyCode);

        $actual = Utils::getStatusCode($exception, new TextResponse('I am a teapot.', 418));

        Assert::assertEquals(418, $actual);
    }

    public function testGetStatusCodeZeroExpectedStatusCodeFiveHundredReturned(): void
    {
        $statusCode = 0;

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->method('getStatusCode')
            ->willReturn($statusCode);

        $actualStatusCode   = Utils::getStatusCode(new Exception(), $response);
        $expectedStatusCode = 500;

        static::assertEquals($expectedStatusCode, $actualStatusCode);
    }
}
