<?php

/**
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace LaminasTest\Stratigility\Middleware;

use Laminas\Stratigility\Exception\MissingResponsePrototypeException;
use Laminas\Stratigility\Middleware\DoublePassMiddlewareDecorator;
use PHPUnit\Framework\TestCase;

class DoublePassMiddlewareDecoratorExceptionTest extends TestCase
{
    private $autoloadFunctions = [];

    protected function setUp() : void
    {
        $this->autoloadFunctions = spl_autoload_functions();
        foreach ($this->autoloadFunctions as $func) {
            spl_autoload_unregister($func);
        }
    }

    protected function tearDown() : void
    {
        foreach ($this->autoloadFunctions as $func) {
            spl_autoload_register($func);
        }
    }

    public function testDiactorosIsNotAvailableAndResponsePrototypeIsNotSet()
    {
        $middleware = function ($request, $response, $next) {
            return $response;
        };

        $this->expectException(MissingResponsePrototypeException::class);
        $this->expectExceptionMessage(
            'no response prototype provided, and laminas/laminas-diactoros is not installed'
        );
        include_once __DIR__ . '/../../src/Middleware/DoublePassMiddlewareDecorator.php';
        new DoublePassMiddlewareDecorator($middleware);
    }
}
