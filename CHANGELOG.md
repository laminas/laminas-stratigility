# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

Versions prior to 1.0 were originally released as `phly/conduit`; please visit
its [CHANGELOG](https://github.com/phly/conduit/blob/master/CHANGELOG.md) for
details.

## 3.5.0 - 2021-09-14


-----

### Release Notes for [3.5.0](https://github.com/laminas/laminas-stratigility/milestone/6)

Feature release (minor)

### 3.5.0

- Total issues resolved: **0**
- Total pull requests resolved: **1**
- Total contributors: **1**

#### Enhancement

 - [22: PHP 8.1 support](https://github.com/laminas/laminas-stratigility/pull/22) thanks to @boesing

## 3.4.0 - 2021-06-28


-----

### Release Notes for [3.4.0](https://github.com/laminas/laminas-stratigility/milestone/3)

### Fixed

- Fixes `Utils::getStatusCode()` to verify that the code returned from a `Throwable` is actually an integer before attempting to see if it falls in the HTTP response status code range. If not, it will return the status from the provided response instance.

### 3.4.0

- Total issues resolved: **0**
- Total pull requests resolved: **4**
- Total contributors: **2**

#### Enhancement

 - [20: Psalm integration](https://github.com/laminas/laminas-stratigility/pull/20) thanks to @ghostwriter
 - [19: Add GitHub CI](https://github.com/laminas/laminas-stratigility/pull/19) thanks to @ghostwriter
 - [16: Remove file headers](https://github.com/laminas/laminas-stratigility/pull/16) thanks to @ghostwriter

#### Bug

 - [15: Proposed fix for #14](https://github.com/laminas/laminas-stratigility/pull/15) thanks to @timdev

## 3.3.0 - 2020-10-20

### Added

- [#13](https://github.com/laminas/laminas-stratigility/pull/13) Adds support for PHP 8

### Changed

- [#8](https://github.com/laminas/laminas-stratigility/pull/8) As the `NotFoundHandler` is technically a request handler, we are moving it to the `Handler` namespace with only implementing the `RequestHandlerInterface` instead of the `MiddlewareInterface`.

### Deprecated

- [#8](https://github.com/laminas/laminas-stratigility/pull/8) Marking `NotFoundHandler` in the `Middleware` namespace as deprecated in favor of the new `NotFoundHandler` in the `Handler` namespace.


-----

### Release Notes for [3.3.0](https://github.com/laminas/laminas-stratigility/milestone/1)



### 3.3.0

- Total issues resolved: **0**
- Total pull requests resolved: **2**
- Total contributors: **2**

#### Enhancement,hacktoberfest-accepted

 - [13: feat: add PHP 8 support](https://github.com/laminas/laminas-stratigility/pull/13) thanks to @alexraputa

#### Enhancement

 - [8: Moving NotFoundHandler to Handler namespace](https://github.com/laminas/laminas-stratigility/pull/8) thanks to @boesing

## 3.2.2 - 2020-03-29

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Fixed `replace` version constraint in composer.json so repository can be used as replacement of `zendframework/zend-stratigility:^3.2.0`.

## 3.2.1 - 2020-01-07

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Fixes all legacy proxying function definitions to add missing return statements.

## 3.2.0 - 2019-06-12

### Added

- Nothing.

### Changed

- [zendframework/zend-stratigility#186](https://github.com/zendframework/zend-stratigility/pull/186) adds a safeguard to middleware pipes to prevent them from being called
  multiple times within the same middleware. As an example, consider the
  following middleware:

  ```php
  public function process(
      ServerRequestInterface $request,
      RequestHandlerInterface $handler
  ) : Response Interface {
      $session = $request->getAttribute('session');
      if (! $session) {
          $response = $handler->handle($request);
      }

      // Inject another attribute before handling
      $response = $handler->handle($request->withAttribute(
          'sessionWasEnabled',
          true
      );
      return $response;
  }
  ```

  When using Stratigility, the `$handler` is an instance of
  `Laminas\Stratigility\Next`, which encapsulates the middleware pipeline and
  advances through it on each call to `handle()`.

  The example demonstrates a subtle error: the response from the first
  conditional should have been returned, but wasn't, which has led to invoking
  the handler a second time. This scenario can have unexpected behaviors,
  including always returning a "not found" response, or returning a response
  from a handler that was not supposed to execute (as an earlier middleware
  already returned early in the original call).

  These bugs are hard to locate, as calling `handle()` is a normal part of any
  middleware, and multiple conditional calls to it are a standard workflow.
  
  With this new version, `Next` will pass a **clone** of itself to the next
  middleware in the pipeline, and unset its own internal pipeline queue. Any
  subsequent requests to `handle()` within the same scope will therefore result
  in the exception `Laminas\Stratigility\Exception\MiddlewarePipeNextHandlerAlreadyCalledException`.

  If you depended on calling `$handler->handle()` multiple times in succession
  within middleware, we recommend that you compose the specific pipeline(s)
  and/or handler(s) you wish to call as class dependencies.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 3.1.0 - 2019-02-06

### Added

- [zendframework/zend-stratigility#178](https://github.com/zendframework/zend-stratigility/pull/178) adds the class `Laminas\Stratigility\EmptyPipelineHandler`, which raises an
  `EmptyPipelineException` when it handles an incoming request. It's primary
  purpose is for use in the `MiddlewarePipe` as a fallback handler during
  `handle()` operations.

### Changed

- [zendframework/zend-stratigility#178](https://github.com/zendframework/zend-stratigility/pull/178) provides some performance improvements to `MiddlewarePipe::handle()` by
  having it create an instance of `EmptyPipelineHandler` to use as a fallback
  handler when it calls `process()` on itself. This prevents cloning of the
  pipeline in this scenario, which is used when it acts as an application
  entrypoint.

- [zendframework/zend-stratigility#185](https://github.com/zendframework/zend-stratigility/pull/185) removes the "final" declaration from the `ErrorHandler` class, to allow
  more easily mocking it for testing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 3.0.3 - 2019-02-06

### Added

- [zendframework/zend-stratigility#184](https://github.com/zendframework/zend-stratigility/pull/184) adds support for PHP 7.3.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 3.0.2 - 2018-07-24

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- [zendframework/zend-stratigility#177](https://github.com/zendframework/zend-stratigility/pull/177) removes a conditional from `Laminas\Stratigility\Middleware\ErrorHandler` that can
  never be reached.

### Fixed

- Nothing.

## 3.0.1 - 2018-04-04

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-stratigility#165](https://github.com/zendframework/zend-stratigility/pull/165) fixes an
  issue with the `PathMiddlewareDecorator` whereby it was using the original
  request when invoking the handler it creates, instead of prepending the
  configured path prefix to the request URI created. With the fix, if middleware
  alters the request path passed to the handler, the changes will now propagate
  to later middleware. As an example:

  ```php
  new PathMiddlewareDecorator('/api', middleware(function ($request, $handler) {
      $uri = $request->getUri();
      if (! preg_match('#^/v\d+/#', $uri->getPath())) {
          $request = $request->withUri($uri->withPath('/v1' . $uri->getPath()));
      }
      return $handler->handle($request);
  }));
  ```

  For the request path `/api/books`, the above will now correctly result in
  `/api/v1/books` being propagated to lower layers of the application, instead
  of `/api/books`.

## 3.0.0 - 2018-03-15

### Added

- [zendframework/zend-stratigility#146](https://github.com/zendframework/zend-stratigility/pull/146) adds a new
  interface, `Laminas\Stratigility\MiddlewarePipeInterface`. It extends the PSR-15
  `MiddlewareInterface` and `RequestHandlerInterface`, and defines one
  additional method, `pipe(MiddlewareInterface $middleware) : void`.

- [zendframework/zend-stratigility#150](https://github.com/zendframework/zend-stratigility/pull/150) adds a new
  class, `Laminas\Stratigility\Middleware\RequestHandlerMiddleware`. The class
  implements the PSR-15 `RequestHandlerInterface` and `MiddlewareInterface`, and
  accepts a single constructor argument, a `RequestHandlerInterface` instance.
  Each of its `handle()` and `process()` methods proxy to the composed request
  handler's `handle()` method, returning its result.

  This class can be useful for adapting request handlers to use within
  pipelines.

- [zendframework/zend-stratigility#142](https://github.com/zendframework/zend-stratigility/pull/142) adds a new
  class, `Laminas\Stratigility\Middleware\HostMiddlewareDecorator`, which provides
  host segregation functionality for middleware, allowing conditional execution
  of middleware only if the requested host matches a configured host.

  ```php
  // Only process $middleware if the request host matches 'example.com':
  $pipeline->pipe(new HostMiddlewareDecorator('example.com', $middleware));
  ```

  Additionally, the patch provides a utility function,
  `Laminas\Stratigility\host()`, to simplify the above declaration:

  ```php
  $pipeline->pipe(host('example.com', $middleware));
  ```

- [zendframework/zend-stratigility#128](https://github.com/zendframework/zend-stratigility/pull/128) adds a
  marker interface, `Laminas\Stratigility\Exception\ExceptionInterface`; all
  package exceptions now implement this interface, allowing you to catch all
  package-related exceptions by typehinting against it.

### Changed

- [zendframework/zend-stratigility#145](https://github.com/zendframework/zend-stratigility/pull/145) updates
  the component to implement and consume **ONLY** PSR-15 interfaces;
  http-interop interfaces and callable middleware are no longer directly
  supported (though Stratigility provides decorators for the latter in order to
  cast them to PSR-15 implementations).

- [zendframework/zend-stratigility#134](https://github.com/zendframework/zend-stratigility/pull/134) and
  [zendframework/zend-stratigility#146](https://github.com/zendframework/zend-stratigility/pull/146) modify
  `MiddlewarePipe` in two ways: it now implements the new
  `MiddlewarePipeInterface`, and is marked as `final`, disallowing direct
  extension. Either decorate an instance in a custom `MiddlewarePipeInterface`
  implementation, or create a custom PSR-15 `MiddlewareInterface`
  implementation if piping is not necessary or will allow additional types.

- [zendframework/zend-stratigility#155](https://github.com/zendframework/zend-stratigility/pull/155) modifies
  each of the following classes to mark them `final`:

  - `Laminas\Stratigility\Middleware\CallableMiddlewareDecorator`
  - `Laminas\Stratigility\Middleware\DoublePassMiddlewareDecorator`
  - `Laminas\Stratigility\Middleware\HostMiddlewareDecorator`
  - `Laminas\Stratigility\Middleware\NotFoundHandler`
  - `Laminas\Stratigility\Middleware\OriginalMessages`
  - `Laminas\Stratigility\Middleware\PathMiddlewareDecorator`
  - `Laminas\Stratigility\Middleware\RequestHandlerMiddleware`
  - `Laminas\Stratigility\Next`

- [zendframework/zend-stratigility#134](https://github.com/zendframework/zend-stratigility/pull/134),
  [zendframework/zend-stratigility#145](https://github.com/zendframework/zend-stratigility/pull/145), and
  [zendframework/zend-stratigility#146](https://github.com/zendframework/zend-stratigility/pull/146) update
  `MiddlewarePipe` to implement `Psr\Http\Server\RequestHandlerInterface`.
  Calling it will cause it to pull the first middleware off the queue and create
  a `Next` implementation that uses the remaining queue as the request handler;
  it then processes the middleware.

- [zendframework/zend-stratigility#134](https://github.com/zendframework/zend-stratigility/pull/134) removes
  the ability to specify a path when calling `pipe()`; use the
  `PathMiddlewareDecorator` or `path()` utility function to pipe middleware with
  path segregation.

- [zendframework/zend-stratigility#153](https://github.com/zendframework/zend-stratigility/pull/153) modifies
  the first argument of the `Mezzio\Middleware\ErrorHandler` and
  `NotFoundHandler` classes. Previously, they each expected a
  `Psr\Http\Message\ResponseInterface` instance; they now both expect a PHP
  callable capable of producing such an instance. This change was done to
  simplify re-use of a service for producing unique response instances within
  dependency injection containers.

- [zendframework/zend-stratigility#157](https://github.com/zendframework/zend-stratigility/pull/157) marks the
  package as conflicting with zendframework/zend-diactoros versions less than
  1.7.1. This is due to the fact that that version provides a bugfix for its
  `Uri::getHost()` implementation that ensures it follows the PSR-7 and IETF RFC
  3986 specifications.

### Deprecated

- Nothing.

### Removed

- [zendframework/zend-stratigility#163](https://github.com/zendframework/zend-stratigility/pull/163) removes
  `Laminas\Stratigility\Middleware\PathRequestHandlerDecorator`, as it was
  deprecated in 2.2, and no longer used with the 3.0 code base.

- [zendframework/zend-stratigility#122](https://github.com/zendframework/zend-stratigility/pull/122) removes
  support for PHP versions 5.6, 7.0, as well as HHVM.

- [zendframework/zend-stratigility#122](https://github.com/zendframework/zend-stratigility/pull/122) removes
  the following classes:
  - `Laminas\Stratigility\Delegate\CallableDelegateDecorator`
  - `Laminas\Stratigility\Exception\InvalidRequestTypeException`
  - `Laminas\Stratigility\Exception\MissingResponsePrototypeException`
  - `Laminas\Stratigility\MiddlewareInterface`
  - `Laminas\Stratigility\Middleware\CallableInteropMiddlewareWrapper`
  - `Laminas\Stratigility\Middleware\CallableMiddlewareWrapper`
  - `Laminas\Stratigility\Middleware\CallableMiddlewareWrapperFactory`
  - `Laminas\Stratigility\NoopFinalHandler`

- [zendframework/zend-stratigility#134](https://github.com/zendframework/zend-stratigility/pull/134) removes
  the class `Laminas\Stratigility\Route`. This was an internal message passed
  between a `MiddlewarePipe` and `Next` instance, and its removal should not
  affect end users.

- [zendframework/zend-stratigility#134](https://github.com/zendframework/zend-stratigility/pull/134) removes
  `Laminas\Stratigility\Exception\InvalidMiddlewareException`, as the exception is
  no longer raised by `MiddlewarePipe`.

### Fixed

- Nothing.

## 2.2.0 - 2018-03-12

### Added

- [zendframework/zend-stratigility#140](https://github.com/zendframework/zend-stratigility/pull/140) adds the
  class `Laminas\Stratigility\Middleware\CallableMiddlewareDecorator` for the
  purpose of decorating callable, standards-signature middleware for use with
  a `MiddlewarePipe` instance. Instantiate it directly, passing the callable
  middleware as the sole argument, or use the `Laminas\Stratigility\middleware()`
  utility function to generate the instance: `middleware($callable)`.

- [zendframework/zend-stratigility#140](https://github.com/zendframework/zend-stratigility/pull/140) adds the
  class `Laminas\Stratigility\Middleware\DoublePassMiddlewareDecorator` for the
  purpose of decorating callable, double-pass middleware for use with
  a `MiddlewarePipe` instance. Instantiate it directly, passing the callable
  middleware and a response instance as arguments, or use the
  `Laminas\Stratigility\doublePassMiddleware()` utility function to generate the
  instance: `doublePassMiddleware($callable, $response)`.

- [zendframework/zend-stratigility#140](https://github.com/zendframework/zend-stratigility/pull/140) adds the
  class `Laminas\Stratigility\Middleware\PathMiddlewareDecorator` for the purposes
  of creating path-segregated middleware. The constructor expects a string path
  literal as the first argument, and an
  `Interop\Http\Server\MiddlewareInterface` instance for the second argument.
  Alternately, use the `Laminas\Stratigility\path()` utility function to generate
  the instance: `path('/foo', $middleware)`.

  This decorator class replaces usage of the `$path` argument to
  `MiddlewarePipe::pipe()`, and should be used to ensure your application is
  forwards-compatible with the upcoming version 3 release.

### Changed

- Nothing.

### Deprecated

- [zendframework/zend-stratigility#140](https://github.com/zendframework/zend-stratigility/pull/140) deprecates
  the class `Laminas\Stratigility\Route`. This class is an internal detail, and will
  be removed in version 3.0.0.

- [zendframework/zend-stratigility#140](https://github.com/zendframework/zend-stratigility/pull/140) deprecates
  the class `Laminas\Stratigility\Exception\InvalidMiddlewareException`. This class
  will be removed in version 3.0.0 as it will no longer be necessary due to
  typehint usage.

- [zendframework/zend-stratigility#140](https://github.com/zendframework/zend-stratigility/pull/140) deprecates
  the class `Laminas\Stratigility\Exception\InvalidRequestTypeException` as it is
  no longer used by the package. It will be removed in version 3.0.0.

- [zendframework/zend-stratigility#140](https://github.com/zendframework/zend-stratigility/pull/140) deprecates
  the class `Laminas\Stratigility\Middleware\CallableInteropMiddlewareWrapper` as it is
  based on interfaces that will no longer be used starting in version 3.0.0. It
  will be removed in version 3.0.0. Please use the new class
  `Laminas\Stratigility\Middleware\CallableMiddlewareDecorator`, or the utility
  function `middleware()`, to decorate callable standards-signature middleware.

- [zendframework/zend-stratigility#140](https://github.com/zendframework/zend-stratigility/pull/140) deprecates
  the class `Laminas\Stratigility\Middleware\CallableMiddlewareWrapper` as it is
  based on interfaces that will no longer be used starting in version 3.0.0. It
  will be removed in version 3.0.0. Please use the new class
  `Laminas\Stratigility\Middleware\DoublePassMiddlewareDecorator`, or the utility
  function `doublePassMiddleware()`, to decorate callable double pass middleware.

- [zendframework/zend-stratigility#140](https://github.com/zendframework/zend-stratigility/pull/140) deprecates
  the class `Laminas\Stratigility\Middleware\CallableMiddlewareWrapperFactory` as
  the class it is associated will be removed starting in version 3.0.0. The
  class will be removed in version 3.0.0.

- [zendframework/zend-stratigility#140](https://github.com/zendframework/zend-stratigility/pull/140) deprecates
  the class `Laminas\Stratigility\NoopFinalHandler` as the class will be removed
  starting in version 3.0.0.

- [zendframework/zend-stratigility#140](https://github.com/zendframework/zend-stratigility/pull/140) deprecates
  the two-argument form of `Laminas\Stratigility\MiddlewarePipe::pipe()`. If you
  need to perform path segregation, use the
  `Laminas\Stratigility\Middleware\PathMiddlewareDecorator` class and/or the
  `Laminas\Stratigility\path()` function to decorate your middleware in order to
  provide path segregation.

- [zendframework/zend-stratigility#140](https://github.com/zendframework/zend-stratigility/pull/140) deprecates
  the piping of double pass middleware directly to `pipe()`; decorate your
  double-pass middleware using `Laminas\Stratigility\Middleware\DoublePassMiddleware`
  or `Laminas\Stratigility\doublePassMiddleware()` prior to piping.

- [zendframework/zend-stratigility#159](https://github.com/zendframework/zend-stratigility/pull/159) deprecates
  `Laminas\Stratigility\MiddlewarePipe::setCallableMiddlewareDecorator()`. Use
  `Laminas\Stratigility\doublePassMiddleware()` or  `Laminas\Stratigility\Middleware\DoublePassMiddleware`
  prior to passing your double-pass middleware to `MiddlewarePipe::pipe()`.

- [zendframework/zend-stratigility#159](https://github.com/zendframework/zend-stratigility/pull/159) deprecates
  `Laminas\Stratigility\MiddlewarePipe::setResponsePrototype()`. This was used only
  to seed an instance of `Laminas\Stratigility\Middleware\CallableMiddlewareWrapperFactory`
  previously; pass your response prototype directly to a new instance of
  `Laminas\Stratigility\Middleware\DoublePassMiddleware` or the ``Laminas\Stratigility\doublePassMiddleware()`
  function instead.

- [zendframework/zend-stratigility#159](https://github.com/zendframework/zend-stratigility/pull/159) deprecates
  `Laminas\Stratigility\MiddlewarePipe::hasResponsePrototype()`.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.1.2 - 2017-10-12

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-stratigility#119](https://github.com/zendframework/zend-stratigility/pull/119) updates to
  webimpress/http-middleware-compatibility `^0.1.3`. This was done to ensure
  backwards compatibilty by injecting the project `composer.json` with the
  currently installed version of http-interop/http-middleware, and in cases
  where that package is not yet installed, prompting the user to install it.
  This approach provides a tiered migration path to http-middleware 0.5.0 for
  users.

## 2.1.1 - 2017-10-10

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-stratigility#118](https://github.com/zendframework/zend-stratigility/pull/118) fixes how
  the `MiddlewarePipe` detects if the second parameter of callable middleware is
  a delegate/request handler when choosing whether or not to decorate it to
  ensure that it will properly decorate it when used with
  http-interop/http-middleware 0.5.0

## 2.1.0 - 2017-10-09

### Added

- [zendframework/zend-stratigility#112](https://github.com/zendframework/zend-stratigility/pull/112) adds
  support for http-interop/http-middleware 0.5.0 via a polyfill provided by the
  package webimpress/http-middleware-compatibility. Essentially, this means you
  can drop this package into an application targeting either the 0.4.1 or 0.5.0
  versions of http-middleware, and it will "just work".

- Adds support for PHP 7.2.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Removes support for HHVM.

- [zendframework/zend-stratigility#107](https://github.com/zendframework/zend-stratigility/pull/107) removes
  the unused `$raiseThrowables` property from `Laminas\Stratigility\Next`.

### Fixed

- Nothing.

## 2.0.1 - 2017-01-25

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-stratigility#98](https://github.com/zendframework/zend-stratigility/pull/98) fixes how
  `Middleware::pipe()` handles `MiddlewarePipe` instances passed to it;
  previously it was incorrectly wrapping them in `CallableMiddlewareWrapper`
  instances; it now pipes them as-is.

## 2.0.0 - 2017-01-24

### Added

- Nothing.

### Changed

- [zendframework/zend-stratigility#96](https://github.com/zendframework/zend-stratigility/pull/96) changes the
  minimum supported http-interop/http-middleware version to 0.4.1. This impacts
  several things:

  - Middleware that implemented the http-interop/http-middleware 0.2.0
    interfaces will no longer work with Stratigility. In most cases, these can
    be updated by changing import statements. As an example:

    ```php
    // http-middleware 0.2.0:
    use Interop\Http\Middleware\DelegateInterface;
    use Interop\Http\Middleware\ServerMiddlewareInterface;

    // Becomes the following under 0.4.1:
    use Interop\Http\ServerMiddleware\DelegateInterface;
    use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
    ```

  - The various classes under `Laminas\Stratigility\Middleware` now implement the
    new interfaces, which could affect extending classes.

  - `Laminas\Stratigility\Next` and `Laminas\Stratigility\Delegate\CallableDelegateDecorator`
    have signature changes due to changes in the `DelegateInterface`; again,
    these changes should only affect those extending the classes.

  - `Interop\Http\Middleware\MiddlewareInterface` (which was intended for
    implementation by client-side middleware) no longer exists, which means
    it is also no longer supported within Stratigility.

- [zendframework/zend-stratigility#67](https://github.com/zendframework/zend-stratigility/pull/67) updates each
  of `Laminas\Stratigility\MiddlewarePipe`, `Laminas\Stratigility\Middleware\ErrorHandler`,
  and `Laminas\Stratigility\Middleware\NotFoundHandler` to require all arguments
  (none are optional).

- [zendframework/zend-stratigility#67](https://github.com/zendframework/zend-stratigility/pull/67) modifies
  the internals of `Laminas\Stratigility\MiddlewarePipe`'s `__invoke()` method.

  - When instantiating the `Next` instance, it now captures it in a variable
    named `$layer`.
  - If the result of `Next` is not a response instance, the response passed
    during invocation is promoted as the layer response.
  - The response is then passed to the `$next` argument provided at invocation,
    and the result of that returned without verification.

  In most cases, this should have no impact on your application.

- [zendframework/zend-stratigility#71](https://github.com/zendframework/zend-stratigility/pull/71) modifies
  `Laminas\Stratigility\MiddlewarePipe` such that it no longer decorates the
  request and response provided at invocation with the
  `Laminas\Stratigility\Http\*` variants, as these have been removed.

- [zendframework/zend-stratigility#76](https://github.com/zendframework/zend-stratigility/pull/76) updates
  `MiddlewarePipe` to implement only the http-interop/http-middleware
  server-side middleware interface, and not the Stratigility-specific
  `MiddlewareInterface` (which was removed).

- [zendframework/zend-stratigility#76](https://github.com/zendframework/zend-stratigility/pull/76) updates
  `Laminas\Stratigility\Middleware\ErrorHandler` to implement the
  http-interop/http-middleware server-side middleware interface instead of the
  Stratigility-specific `MiddlewareInterface` (which was removed).

- [zendframework/zend-stratigility#76](https://github.com/zendframework/zend-stratigility/pull/76) updates
  `Laminas\Stratigility\Middleware\NotFoundHandler` to implement the
  http-interop/http-middleware server-side middleware interface instead of the
  Stratigility-specific `MiddlewareInterface` (which was removed).

- [zendframework/zend-stratigility#76](https://github.com/zendframework/zend-stratigility/pull/76) updates
  `MiddlewarePipe::__invoke()` to require a third argument, now named
  `$delegate`, and no longer type-hinted. If a callable not implementing
  http-interop/http-middleware `DelegateInterface` is provided, it is wrapped in
  the `CallableDelegateDecorator` (introduced in 1.3.0). The method then calls
  its own `process()` method with the request and delegate. This method should
  typically only be used as an entry point for an application.

- [zendframework/zend-stratigility#76](https://github.com/zendframework/zend-stratigility/pull/76) updates
  `MiddlewarePipe::pipe()` to raise an exception if callable middleware using
  the legacy double-pass signature is provided, but no response prototype is
  composed in the `MiddlewarePipe` instance yet.

- [zendframework/zend-stratigility#76](https://github.com/zendframework/zend-stratigility/pull/76) updates
  the constructor of `Next` to rename the `$done` argument to `$nextDelegate`
  and typehint it against the http-interop/http-middleware `DelegateInterface`.

- [zendframework/zend-stratigility#76](https://github.com/zendframework/zend-stratigility/pull/76) updates
  `Next::__invoke()` to remove all arguments except the `$request` argument; the
  method now proxies to the instance `process()` method.

- [zendframework/zend-stratigility#76](https://github.com/zendframework/zend-stratigility/pull/76) updates
  `Next` to no longer compose a `Dispatch` instance; it is now capable of
  dispatching on its own.

- [zendframework/zend-stratigility#76](https://github.com/zendframework/zend-stratigility/pull/76) updates the
  `Laminas\Stratigility\Route` constructor to raise an exception if
  non-http-interop middleware is provided as the route handler.

- [zendframework/zend-stratigility#79](https://github.com/zendframework/zend-stratigility/pull/79) updates the
  `raiseThrowables()` method of each of `MiddlewarePipe` and `Next` to be
  no-ops.

### Deprecated

- [zendframework/zend-stratigility#79](https://github.com/zendframework/zend-stratigility/pull/79) deprecates
  the `raiseThrowables()` method of each of `MiddlewarePipe` and `Next`.

### Removed

- `Laminas\Stratigility\Exception\MiddlewareException` was removed as it is no
  longer thrown.

- [zendframework/zend-stratigility#67](https://github.com/zendframework/zend-stratigility/pull/67) removes
  `Laminas\Stratigility\FinalHandler`. Use `Laminas\Stratigility\NoopFinalHandler`
  instead, along with `Laminas\Stratigility\Middleware\ErrorHandler` and
  `Laminas\Stratigility\Middleware\NotFoundHandler` (or equivalents).

- [zendframework/zend-stratigility#67](https://github.com/zendframework/zend-stratigility/pull/67) removes
  `Laminas\Stratigility\ErrorMiddlewareInterface`. Register middleware, such as
  `Laminas\Stratigility\Middleware\ErrorHandler`, in outer layers of your
  application to handle errors.

- [zendframework/zend-stratigility#67](https://github.com/zendframework/zend-stratigility/pull/67) removes
  `Laminas\Stratigility\Dispatch`. This was an internal detail of the `Next`
  implementation, and should not affect most applications.

- [zendframework/zend-stratigility#67](https://github.com/zendframework/zend-stratigility/pull/67) removes
  `Laminas\Stratigility\Utils::getArity()`. This was used only in `Dispatch`;
  since middleware signatures no longer vary, it is no longer necessary.

- [zendframework/zend-stratigility#67](https://github.com/zendframework/zend-stratigility/pull/67) removes
  the final, optional `$err` argument to `Laminas\Stratigility\Next()`; raise
  exceptions instead, and provide error handling middleware such as
  `Laminas\Stratigility\Middleware\ErrorHandler` instead.

- [zendframework/zend-stratigility#67](https://github.com/zendframework/zend-stratigility/pull/67) removes
  the `$done` argument to the `Laminas\Stratigility\Next` constructor.

- [zendframework/zend-stratigility#71](https://github.com/zendframework/zend-stratigility/pull/71) removes
  the `Laminas\Stratigility\Http\Request` class.

- [zendframework/zend-stratigility#71](https://github.com/zendframework/zend-stratigility/pull/71) removes
  the `Laminas\Stratigility\Http\Response` class.

- [zendframework/zend-stratigility#71](https://github.com/zendframework/zend-stratigility/pull/71) removes
  `Laminas\Stratigility\Http\ResponseInterface`.

- [zendframework/zend-stratigility#76](https://github.com/zendframework/zend-stratigility/pull/76) removes
  `Laminas\Stratigility\MiddlewareInterface` and `Laminas\Stratigility\ErrorMiddlewareInterface`.
  The latter is removed entirely, while the former is essentially replaced by
  http-interop's `ServerMiddlewareInterface`. You may still write callable
  middleware using the legacy double-pass signature, however.

- [zendframework/zend-stratigility#76](https://github.com/zendframework/zend-stratigility/pull/76) removes the
  `Laminas\Stratigility\Dispatch` class. The class was an internal detail of
  `Next`, and no longer required.

### Fixed

- Nothing.

## 1.3.3 - 2017-01-23

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-stratigility#86](https://github.com/zendframework/zend-stratigility/pull/86) fixes the
  links to documentation in several exception messages to ensure they will be
  useful to developers.

## 1.3.2 - 2017-01-05

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-stratigility#95](https://github.com/zendframework/zend-stratigility/pull/95) fixes an
  issue with how the `$err` is dealt with. Specifically, if an error arises,
  then subsequent middlewares should be dispatched as callables. Without this
  fix, stratigility would simply continue dispatching middlewares, ignoring
  the failing ones.

## 1.3.1 - 2016-11-10

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-stratigility#85](https://github.com/zendframework/zend-stratigility/pull/85) fixes an
  issue with how the `$done` or `$nextDelegate` is invoked by `Next` when an
  error is present. Previously, the class was detecting a `Next` instance as an
  http-interop `DelegateInterface` instance and dropping the error; this would
  then mean if the instance contained error middleware, it would never be
  dispatched.

## 1.3.0 - 2016-11-10

### Added

- [zendframework/zend-stratigility#66](https://github.com/zendframework/zend-stratigility/pull/66) adds a new
  class, `Laminas\Stratigility\Middleware\NotFoundHandler`. This class may be piped
  into an application at an innermost layer; when invoked, it will return a 404
  plain text response.

- [zendframework/zend-stratigility#66](https://github.com/zendframework/zend-stratigility/pull/66) adds a new
  class, `Laminas\Stratigility\Middleware\ErrorHandler`. This class may be piped
  into an application, typically at the outermost or one of the outermost
  layers. When invoked, it does the following:

  - Creates a PHP error handler that will re-throw PHP errors as
    `ErrorExceptions`.
  - Dispatches to the next layer.
  - If the next layer does not return a response, it raises a new
    `MissingResponseException`.
  - Catches all exceptions from calling the next layer, and passes them to an
    error response generator to return an error response.

  A default error response generator is provided, which will return a 5XX series
  response in plain text. You may provide a callable generator to the
  constructor in order to customize the response generated; please refer to the
  documentation for details.

- [zendframework/zend-stratigility#66](https://github.com/zendframework/zend-stratigility/pull/66) adds a new
  class, `Laminas\Stratigility\NoopFinalHandler`. This class may be provided as the
  `$out` argument to a `MiddlewarePipe`, or as the final handler to
  `Laminas\Diactoros\Server::listen()` (in which case it will be passed to the
  middleware you invoke as the application). This handler returns the response
  provided to it verbatim.

- [zendframework/zend-stratigility#70](https://github.com/zendframework/zend-stratigility/pull/70) adds a new
  class, `Laminas\Stratigility\Middleware\OriginalMessages`. Compose this
  middleware in an outermost layer, and it will inject the following attributes
  in the request passed to nested layers:

  - `originalRequest`, representing the request provided to it.
  - `originalResponse`, representing the response provided to it.
  - `originalUri`, representing URI instance composed in the request provided to it.

- [zendframework/zend-stratigility#75](https://github.com/zendframework/zend-stratigility/pull/75) adds support
  for [http-interop middleware 0.2.0](https://github.com/http-interop/http-middleware/tree/ff545c87e97bf4d88f0cb7eb3e89f99aaa53d7a9).
  For full details, see the [migration guide](https://docs.laminas.dev/laminas-stratigility/migration/to-v2/#http-interop-compatibility).
  As a summary of features:
  - You may now pipe http-interop middleware to `MiddlewarePipe` instances.
  - You may now pipe callable middleware that defines the same signature as
    http-interop middleware to `MiddlewarePipe` instances; these will be
    decorated in a `Laminas\Stratigility\Middleware\CallableInteropMiddlewareWrapper`
    instance.
  - `MiddlewarePipe` now implements the http-interop
    `ServerMiddlewareInterface`, allowing it to be used in http-interop
    middleware dispatchers.

- [zendframework/zend-stratigility#75](https://github.com/zendframework/zend-stratigility/pull/75) adds the
  class `Laminas\Stratigility\Middleware\CallableMiddlewareWrapper`. It accepts
  callable double-pass middleware and a response prototype, and implements the
  http-interop `ServerMiddlewareInterface`, allowing you to adapt existing
  callable middleware to work with http-interop middleware dispatchers.

- [zendframework/zend-stratigility#75](https://github.com/zendframework/zend-stratigility/pull/75) adds the
  class `Laminas\Stratigility\Middleware\CallableInteropMiddlewareWrapper`. It accepts
  callable middleware that follows the http-interop `ServerMiddlewareInterface`,
  and implements that interface itself, to allow composing such middleware in
  http-interop middleware dispatchers.

- [zendframework/zend-stratigility#75](https://github.com/zendframework/zend-stratigility/pull/75) adds the
  class `Laminas\Stratigility\Delegate\CallableDelegateDecorator`, which can be
  used to add http-interop middleware support to your existing callable
  middleware.

- [zendframework/zend-stratigility#75](https://github.com/zendframework/zend-stratigility/pull/75) adds a new
  method to `MiddlewarePipe`, `setResponseProtoype()`. When this method is
  invoked with a PSR-7 response, the following occurs:
  - That response is injected in `Next` and `Dispatch` instances, to allow
    dispatching legacy callable middleware as if it were http-interop
    middleware.
  - Any callable middleware implementing the legacy signature will now be
    decorated using the above `CallableMiddlewareWrapper` in order to adapt it
    as http-interop middleware.

- [zendframework/zend-stratigility#78](https://github.com/zendframework/zend-stratigility/pull/78) adds a new
  method to each of `Laminas\Stratigility\MiddlewarePipe`, `Next`, and `Dispatch`:
  `raiseThrowables()`. When called, `Dispatch` will no longer wrap dispatch of
  middleware in a try/catch block, allowing throwables/exceptions to bubble out.
  This enables the ability to create error handling middleware as an outer layer
  or your application instead of relying on error middleware and/or the final
  handler. Typical usage will be to call the method on the `MiddlewarePipe`
  before dispatching it.

### Changed

- [zendframework/zend-stratigility#70](https://github.com/zendframework/zend-stratigility/pull/70) makes the
  following changes to `Laminas\Stratigility\FinalHandler`:

  - It now pulls the original request using the `originalRequest` attribute,
    instead of `getOriginalRequest()`; see the deprecation of
    `Laminas\Stratigility\Http\Request`, below, for why this works.
  - It no longer writes to the response using the
    `Laminas\Stratigility\Http\Response`-specific `write()` method, but rather
    pulls the message body and writes to that.

- [zendframework/zend-stratigility#75](https://github.com/zendframework/zend-stratigility/pull/75) updates
  `MiddlewarePipe` to inject the `$response` argument to `__invoke()` as the
  response prototype.

- [zendframework/zend-stratigility#75](https://github.com/zendframework/zend-stratigility/pull/75) updates
  `Laminas\Stratigility\Next` to implement the http-interop middleware
  `DelegateInterface`. It also updates `Laminas\Stratigility\Dispatch` to add a new
  method, `process()`, following the `DelegateInterface` signature, thus
  allowing `Next` to properly process http-interop middleware. These methods
  will use the composed response prototype, if present, to invoke callable
  middleware using the legacy signature.

- [zendframework/zend-stratigility#75](https://github.com/zendframework/zend-stratigility/pull/75) updates
  `Next` to allow the `$done` constructor argument to be an http-interop
  `DelegateInterface`, and will invoke it as such if the queue is exhausted.

- [zendframework/zend-stratigility#75](https://github.com/zendframework/zend-stratigility/pull/75) updates
  `Route` (which is used internally by `MiddlewarePipe` to allow either callable
  or http-interop middleware as route handlers.

### Deprecated

- [zendframework/zend-stratigility#66](https://github.com/zendframework/zend-stratigility/pull/66) deprecates
  the `Laminas\Stratigility\FinalHandler` class. We now recommend using the
  `NoopFinalHandler`, along with the `ErrorHandler` and `NotFoundHandler`
  middleware (or equivalents) to provide a more fine-grained, flexible, error
  handling solution for your applications.

- [zendframework/zend-stratigility#66](https://github.com/zendframework/zend-stratigility/pull/66) deprecates
  the `Laminas\Stratigility\Dispatch` class. This class is used internally by
  `Next`, and deprecation should not affect the majority of users.

- [zendframework/zend-stratigility#66](https://github.com/zendframework/zend-stratigility/pull/66) deprecates
  `Laminas\Stratigility\ErrorMiddlewareInterface`. We recommend instead using
  exceptions, along with the `ErrorHandler`, to provide error handling for your
  application.

- [zendframework/zend-stratigility#66](https://github.com/zendframework/zend-stratigility/pull/66) updates
  `Laminas\Stratigility\MiddlewarePipe::__invoke()` to emit a deprecation notice if
  no `$out` argument is provided, as version 2 will require it.

- [zendframework/zend-stratigility#66](https://github.com/zendframework/zend-stratigility/pull/66) updates
  `Laminas\Stratigility\Next::__invoke()` to emit a deprecation notice if
  a non-null `$err` argument is provided; middleware should raise an exception,
  instead of invoking middleware implementing `ErrorMiddlewareInterface`.

- [zendframework/zend-stratigility#70](https://github.com/zendframework/zend-stratigility/pull/70) deprecates
  `Laminas\Stratigility\Http\Request`. Additionally:

  - The composed "PSR Request" is now injected with an additional attribute,
    `originalRequest`, allowing retrieval using standard PSR-7 attribute access.
  - The methods `getCurrentRequest()` and `getOriginalRequest()` now emit
    deprecation notices when invoked, urging users to update their code.

- [zendframework/zend-stratigility#70](https://github.com/zendframework/zend-stratigility/pull/70) deprecates
  `Laminas\Stratigility\Http\ResponseInterface`.

- [zendframework/zend-stratigility#70](https://github.com/zendframework/zend-stratigility/pull/70) deprecates
  `Laminas\Stratigility\Http\Response`. Additionally, the methods `write()`,
  `end()`, `isComplete()`, and `getOriginalResponse()` now emit deprecation
  notices when invoked, urging users to update their code.

- [zendframework/zend-stratigility#75](https://github.com/zendframework/zend-stratigility/pull/75) deprecates
  the `$response` argument in existing callable middleware. Please only operate
  on the response returned by `$next`/`$delegate`, or create a response. See the
  documentation [section on response arguments](https://docs.laminas.dev/laminas-stratigility/api/#response-argument)
  for more details.

- [zendframework/zend-stratigility#75](https://github.com/zendframework/zend-stratigility/pull/75) deprecates
  usage of error middleware, and thus deprecates the `$err` argument to `$next`;
  explicitly invoking error middleware using that argument to `$next` will now
  raise a deprecation notice.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.2.1 - 2016-03-24

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-stratigility#52](https://github.com/zendframework/zend-stratigility/pull/52) fixes the
  behavior of the `FinalHandler` with regards to exception handling, ensuring
  that the reason phrase reported corresponds to the HTTP status code used.
- [zendframework/zend-stratigility#54](https://github.com/zendframework/zend-stratigility/pull/54) modifies the
  behavior of the `FinalHandler` when creating an error or 404 response to call
  `write()` instead of `end()` on the response object. This fixes a lingering
  issue with emitting the `Content-Length` header from the `SapiEmitter`, as
  well as prevents the `SapiEmitter` from raising exceptions when doing so
  (which was happening starting with 1.2.0).

## 1.2.0 - 2016-03-17

This release contains two potential backwards compatibility breaks:

- In versions prior to 1.2.0, after `Laminas\Stratigility\Http\Response::end()` was
  called, `with*()` operations were performed as no-ops, which led to
  hard-to-detect errors. Starting with 1.2.0, they now raise a
  `RuntimeException`.

- In versions prior to 1.2.0, `Laminas\Stratigility\FinalHandler` always provided
  exception details in the response payload for errors. Starting with 1.2.0, it
  only does so if not in a production environment (which is the default
  environment).

### Added

- [zendframework/zend-stratigility#36](https://github.com/zendframework/zend-stratigility/pull/36) adds a new
  `InvalidMiddlewareException`, with the static factory `fromValue()` that
  provides an exception message detailing the invalid type. `MiddlewarePipe` now
  throws this exception from the `pipe()` method when a non-callable value is
  provided.
- [zendframework/zend-stratigility#46](https://github.com/zendframework/zend-stratigility/pull/46) adds
  `FinalHandler::setOriginalResponse()`, allowing you to alter the response used
  for comparisons when the `FinalHandler` is invoked.
- [zendframework/zend-stratigility#37](https://github.com/zendframework/zend-stratigility/pull/37) and
  [zendframework/zend-stratigility#49](https://github.com/zendframework/zend-stratigility/pull/49) add
  support in `Laminas\Stratigility\Dispatch` to catch PHP 7 `Throwable`s.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-stratigility#30](https://github.com/zendframework/zend-stratigility/pull/30) updates the
  `Response` implementation to raise exceptions from `with*()` methods if they
  are called after `end()`.
- [zendframework/zend-stratigility#46](https://github.com/zendframework/zend-stratigility/pull/46) fixes the
  behavior of `FinalHandler::handleError()` to only display exception details
  when not in production environments, and changes the default environment to
  production.

## 1.1.3 - 2016-03-17

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-stratigility#39](https://github.com/zendframework/zend-stratigility/pull/39) updates the
  FinalHandler to ensure that emitted exception messages include previous
  exceptions.

## 1.1.2 - 2015-10-09

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-stratigility#32](https://github.com/zendframework/zend-stratigility/pull/32) updates the
  request and response typehints in `Laminas\Stratigility\Dispatch` to use the
  corresponding PSR-7 interfaces, instead of the Stratigility-specific
  decorators. This fixes issues when calling `$next()` with non-Stratigility
  instances of either.

## 1.1.1 - 2015-08-25

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-stratigility#25](https://github.com/zendframework/zend-stratigility/pull/25) modifies the
  constructor of `Next` to clone the incoming `SplQueue` instance, ensuring the
  original can be re-used for subsequent invocations (e.g., within an async
  listener environment such as React).

## 1.1.0 - 2015-06-25

### Added

- [zendframework/zend-stratigility#13](https://github.com/zendframework/zend-stratigility/pull/13) adds
  `Utils::getStatusCode($error, ResponseInterface $response)`; this static
  method will attempt to use an exception code as an HTTP status code, if it
  falls in a valid HTTP error status range. If the error is not an exception, it
  ensures that the status code is an error status.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-stratigility#12](https://github.com/zendframework/zend-stratigility/pull/12) updates
  `FinalHandler` such that it will return the response provided at invocation
  if it differs from the response at initialization (i.e., a new response
  instance, or if the body size has changed). This allows you to safely call
  `$next()` from all middleware in order to allow post-processing.

## 1.0.2 - 2015-06-24

### Added

- [zendframework/zend-stratigility#14](https://github.com/zendframework/zend-stratigility/pull/14) adds
  [bookdown](http://bookdown.io) documentation.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.0.1 - 2015-06-16

### Added

- [zendframework/zend-stratigility#8](https://github.com/zendframework/zend-stratigility/pull/8) adds a
  `phpcs.xml` PHPCS configuration file, allowing execution of each of `phpcs`
  and `phpcbf` without arguments.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-stratigility#7](https://github.com/zendframework/zend-stratigility/pull/7) ensures that
  arity checks on PHP callables in array format (`[$instance, $method]`,
  `['ClassName', 'method']`) work, as well as on static methods using the string
  syntax (`'ClassName::method'`). This allows them to be used without issue as
  middleware handlers.

## 1.0.0 - 2015-05-14

First stable release, and first relase as `laminas-stratigility`.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.
