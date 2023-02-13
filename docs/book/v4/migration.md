# Migrating from version 3 to version 4

In this document, we outline the backwards breaking changes with version 3.0,
and provide guidance on how to upgrade your application to be compatible.

- [PHP support](#php-support)
- [PSR-17](#psr-17)
- [Changes in public interfaces](#changes-in-public-interfaces)
  - [Signature changes](#signature-changes)
  - [Removed classes and exceptions](#removed-classes-and-exceptions)
  - [Removed functions](#removed-functions)

## PHP support

We now support only PHP versions 7.4 and above.

## PSR-17

Stratigility now supports [PSR-17](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-17-http-factory.md)
interfaces.

As a result, a number of signatures have been changed. Primarily, these were a
matter of updating typehints on `Laminas\Stratigility\Middleware\ErrorHandler`
& `Laminas\Stratigility\Handler\NotFoundHandler`.

```php
use Psr\Http\Message\RequestFactoryInterface;

// Laminas\Stratigility\Middleware\ErrorHandler::__construct

public function __construct(ResponseFactoryInterface $responseFactory, ?callable $responseGenerator = null){}

// Laminas\Stratigility\Handler\NotFoundHandler::__construct
public function __construct(ResponseFactoryInterface $responseFactory){}
```

## Changes in public interfaces

### Signature changes

- `Laminas\Stratigility\Handler\NotFoundHandler::__construct()`: the parameter `$responseFactory` changed from `callable` to `Psr\Http\Message\ResponseFactoryInterface`.
- `Laminas\Stratigility\Middleware\ErrorHandler::__construct()`: the parameter `$responseFactory` changed from `callable` to `Psr\Http\Message\ResponseFactoryInterface`.

### Removed classes and exceptions

The following deprecated classes and exceptions have been removed:

- `Laminas\Stratigility\Middleware\NotFoundHandler`

### Removed functions

The following deprecated utility functions have been removed:

- `Zend\Stratigility::doublePassMiddleware(...)`
- `Zend\Stratigility::host(...)`
- `Zend\Stratigility::middleware(...)`
- `Zend\Stratigility::path(...)`