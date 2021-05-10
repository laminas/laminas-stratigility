<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility\Middleware;

use Laminas\Stratigility\Exception\MissingResponsePrototypeException;
use Laminas\Stratigility\Middleware\DoublePassMiddlewareDecorator;
use PHPUnit\Framework\TestCase;

use function class_exists;
use function spl_autoload_functions;
use function spl_autoload_register;
use function spl_autoload_unregister;

class DoublePassMiddlewareDecoratorExceptionTest extends TestCase
{
    /** @var array */
    private $autoloadFunctions = [];

    protected function setUp() : void
    {
        class_exists(MissingResponsePrototypeException::class);
        class_exists(DoublePassMiddlewareDecorator::class);

        $this->autoloadFunctions = spl_autoload_functions();
        foreach ($this->autoloadFunctions as $func) {
            spl_autoload_unregister($func);
        }
    }

    private function reloadAutoloaders() : void
    {
        foreach ($this->autoloadFunctions as $autoloader) {
            spl_autoload_register($autoloader);
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

        try {
            new DoublePassMiddlewareDecorator($middleware);
        } finally {
            $this->reloadAutoloaders();
        }
    }
}
