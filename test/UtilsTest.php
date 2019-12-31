<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Stratigility;

use Laminas\Stratigility\Dispatch;
use Laminas\Stratigility\MiddlewarePipe;
use Laminas\Stratigility\Utils;
use PHPUnit_Framework_TestCase as TestCase;

class UtilsTest extends TestCase
{
    public function callablesWithVaryingArity()
    {
        return [
            'function' => ['strlen', 1],
            'closure' => [function ($x, $y) {
            }, 2],
            'invokable' => [new Dispatch(), 5],
            'interface' => [new MiddlewarePipe(), 2], // 2 REQUIRED arguments!
        ];
    }

    /**
     * @dataProvider callablesWithVaryingArity
     */
    public function testArity($callable, $expected)
    {
        $this->assertEquals($expected, Utils::getArity($callable));
    }

    public function nonCallables()
    {
        return [
            'null'                => [null],
            'false'               => [false],
            'true'                => [true],
            'int'                 => [1],
            'float'               => [1.1],
            'string'              => ['not a callable'],
            'array'               => [['not a callable']],
            'non-callable-object' => [(object) ['foo' => 'bar']],
        ];
    }

    /**
     * @dataProvider nonCallables
     */
    public function testReturnsZeroForNonCallableArguments($test)
    {
        $this->assertSame(0, Utils::getArity($test));
    }
}
