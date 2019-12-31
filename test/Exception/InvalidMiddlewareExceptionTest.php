<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace LaminasTest\Stratigility\Exception;

use Interop\Http\Server\MiddlewareInterface;
use Laminas\Stratigility\Exception\InvalidMiddlewareException;
use PHPUnit\Framework\TestCase;
use stdClass;

class InvalidMiddlewareExceptionTest extends TestCase
{
    public function invalidMiddlewareValues()
    {
        return [
            'null'         => [null, 'NULL'],
            'true'         => [true, 'boolean'],
            'false'        => [false, 'boolean'],
            'empty-string' => ['', 'string'],
            'string'       => ['not-callable', 'string'],
            'int'          => [1, 'integer'],
            'float'        => [1.1, 'double'],
            'array'        => [['not', 'callable'], 'array'],
            'object'       => [(object) ['not', 'callable'], stdClass::class],
        ];
    }

    /**
     * @dataProvider invalidMiddlewareValues
     *
     * @param mixed $value
     * @param string $expected
     */
    public function testFromValueProvidesNewExceptionWithMessageRelatedToValue($value, $expected)
    {
        $e = InvalidMiddlewareException::fromValue($value);
        $this->assertEquals(sprintf(
            'Middleware must implement %s; received middleware of type %s',
            MiddlewareInterface::class,
            $expected
        ), $e->getMessage());
    }
}
