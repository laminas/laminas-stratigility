<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Stratigility\Exception;

use Laminas\Stratigility\Exception\InvalidMiddlewareException;
use PHPUnit\Framework\TestCase;

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
            'object'       => [(object) ['not', 'callable'], 'stdClass'],
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
            'Middleware must be callable, %s found',
            $expected
        ), $e->getMessage());
    }
}
