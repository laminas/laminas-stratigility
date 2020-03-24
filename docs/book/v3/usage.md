# Usage

Creating an application consists of 3 steps:

- Create middleware or a middleware pipeline
- Create a server, using the middleware
- Instruct the server to listen for a request

```php
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\ResponseFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Laminas\Stratigility\MiddlewarePipe;

require __DIR__ . '/../vendor/autoload.php';

$app    = new MiddlewarePipe();
$server = new RequestHandlerRunner(
    $app,
    new SapiEmitter(),
    function () {
        return ServerRequestFactory::fromGlobals();
    },
    function (\Throwable $e) {
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
