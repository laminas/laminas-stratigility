# Usage

<!-- markdownlint-disable-next-line heading-increment -->
> ### Installation requirements
>
> The following example depends on the [laminas-httphandlerrunner](http://docs.laminas.dev/laminas-httphandlerrunner)
> component. You can install it with the following command:
>
> ```bash
> $ composer require laminas/laminas-httphandlerrunner
> ```

Creating an application consists of 3 steps:

- Create middleware or a middleware pipeline
- Create a server, using the middleware
- Instruct the server to listen for a request

```php
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Laminas\Stratigility\MiddlewarePipe;

require __DIR__ . '/../vendor/autoload.php';

$app    = new MiddlewarePipe();
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

The above example is useless by itself until you pipe middleware into the application.
