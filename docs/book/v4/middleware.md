# Middleware

What is middleware?

Middleware is code that exists between the request and response, and which can
take the incoming request, perform actions based on it, and either complete the
response or pass delegation on to the next middleware in the queue.

<!-- markdownlint-disable-next-line header-increment -->
> ### Installation requirements
>
> The following example depends on the [laminas-httphandlerrunner](http://docs.laminas.dev/laminas-httphandlerrunner)
> component. You can install it with the following command:
>
> ```bash
> $ composer require laminas/laminas-httphandlerrunner
> ```

```php
// In public/index.php:
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Laminas\Stratigility\Middleware\NotFoundHandler;
use Laminas\Stratigility\MiddlewarePipe;

use function Laminas\Stratigility\middleware;
use function Laminas\Stratigility\path;

require __DIR__ . '/../vendor/autoload.php';

$app = new MiddlewarePipe();

// Landing page
$app->pipe(middleware(function ($req, $handler) {
    if (! in_array($req->getUri()->getPath(), ['/', ''], true)) {
        return $handler->handle($req);
    }

    $response = new Response();
    $response->getBody()->write('Hello world!');

    return $response;
}));

// Another page
$app->pipe(path('/foo', middleware(function ($req, $handler) {
    $response = new Response();
    $response->getBody()->write('FOO!');

    return $response;
})));

// 404 handler
$app->pipe(new NotFoundHandler(function () {
    return new Response();
}));

$server = new RequestHandlerRunner(
    $app,
    new SapiEmitter(),
    static function () {
        return ServerRequestFactory::fromGlobals();
    },
    static function (\Throwable $e) {
        $response = (new ResponseFactory())->createResponse(500);
        $response->getBody()->write(sprintf(
            'An error occurred: %s',
            $e->getMessage
        ));
        return $response;
    }
);

$server->run();
```

In the above example, we have two examples of middleware. The first is a
landing page, and listens at the root path. If the request path is empty or
`/`, it completes the response. If it is not, it delegates to the next
middleware in the stack. The second middleware matches on the path `/foo`
&mdash; meaning it will match `/foo`, `/foo/`, and any path beneath. In that
case, it will complete the response with its own message. If no paths match at
this point, a "final handler" is composed by default to report 404 status.

So, concisely put, _middleware are PHP callables that accept a request object,
and do something with it, optionally delegating creation of a response to
another handler_.

> ### PSR-15 middleware
>
> Stratigility supports only [PSR-15](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-15-request-handlers.md)
> middleware.

> ### HTTP Handler Runner
>
> The above example, and the one on the [usage page](usage.md), makes use of the
> [laminas/laminas-httphandlerrunner](https://docs.laminas.dev/laminas-httphandlerrunner/)
> package, which provides a flexible way to run applications based on PSR-15
> request handlers.

Middleware can decide more processing can be performed by calling the `$handler`
instance passed during invocation. With this paradigm, you can build a workflow
engine for handling requests &mdash; for instance, you could have middleware
perform the following:

- Handle authentication details
- Perform content negotiation
- Perform HTTP negotiation
- Route the path to a more appropriate, specific handler

Each middleware can itself be middleware.

Using the provided `PathMiddlewareDecorator` (created by the `path()` function
demonstrated in the initial example), you can also attach middleware to specific
paths, allowing you to mix and match applications under a common domain. As an
example, you could put API middleware next to middleware that serves its
documentation, next to middleware that serves files, and segregate each by URI:

```php
$app->pipe(path('/api', $apiMiddleware));
$app->pipe(path('/docs', $apiDocMiddleware));
$app->pipe(path('/files', $filesMiddleware));
```

The handlers in each middleware attached this way will see a URI with that path
segment stripped, allowing them to be developed separately and re-used under
any path you wish.

Within Stratigility, middleware must be
[PSR-15](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-15-request-handlers.md) middleware.
`Laminas\Stratigility\MiddlewarePipe` implements
`Psr\Http\Server\MiddlewareInterface`.
