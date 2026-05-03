<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility\Exception;

use Generator;
use Laminas\Stratigility\Exception\ExceptionInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function basename;
use function glob;
use function is_a;
use function substr;

final class ExceptionTest extends TestCase
{
    /** @return Generator<string, array{0: string}> */
    public static function exception(): Generator
    {
        $namespace = 'Laminas\Stratigility\Exception\\';

        $exceptions = glob(__DIR__ . '/../../src/Exception/*.php');
        self::assertIsArray($exceptions);
        foreach ($exceptions as $exception) {
            $class = substr(basename($exception), 0, -4);

            yield $class => [$namespace . $class];
        }
    }

    #[DataProvider('exception')]
    public function testExceptionIsInstanceOfExceptionInterface(string $exception): void
    {
        self::assertStringContainsString('Exception', $exception);
        self::assertTrue(is_a($exception, ExceptionInterface::class, true));
    }
}
