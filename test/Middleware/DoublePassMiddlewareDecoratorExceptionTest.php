<?php

declare(strict_types=1);

namespace LaminasTest\Stratigility\Middleware;

use Laminas\Stratigility\Exception\MissingResponsePrototypeException;
use Laminas\Stratigility\Middleware\DoublePassMiddlewareDecorator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function class_exists;
use function spl_autoload_functions;
use function spl_autoload_register;
use function spl_autoload_unregister;

class DoublePassMiddlewareDecoratorExceptionTest extends TestCase
{
    /** @var list<callable(string): void> */
    private array $autoloadFunctions = [];

    protected function setUp(): void
    {
        class_exists(MissingResponsePrototypeException::class);
        class_exists(DoublePassMiddlewareDecorator::class);

        $this->autoloadFunctions = spl_autoload_functions();
        foreach ($this->autoloadFunctions as $func) {
            spl_autoload_unregister($func);
        }
    }

    private function reloadAutoloaders(): void
    {
        foreach ($this->autoloadFunctions as $autoloader) {
            spl_autoload_register($autoloader);
        }
    }

    public function testDiactorosIsNotAvailableAndResponsePrototypeIsNotSet(): void
    {
        /** @psalm-suppress UnusedClosureParam */
        $middleware = static fn(
            ServerRequestInterface $request,
            ResponseInterface $response,
            callable $next,
        ): ResponseInterface => $response;

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
