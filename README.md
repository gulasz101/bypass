<div align="center">
    <p>
        <img  src="docs/img/logo.png" alt="Bypass Logo" width="200" />
        <h1>Bypass for PHP</h1>
    </p>
</div>

<p align="center">
    <a href="#about">About</a> |
    <a href="#installation">Installation</a> |
    <a href="#writing-tests">Writing Tests</a> |
    <a href="#examples">Examples</a> |
    <a href="#credits">Credits</a> |
    <a href="#inspired">Inspired</a>
</p>

-------

## Note from @gulasz101

This is shameless fork, as I needed this lib to work with my symfony projects and I did not wanted to install whole `illuminate/support` package.
Also package was build on top of Http facade which was depending on Laravel App.

What I did: replaced `illuminate/http` with `symfony/http-client` and psr compliant libs.

Anyway all credit should go to original author: [@CiaReis](https://github.com/ciareis) 

-------

## About

<table>
  <tr>
    <td>
      <p>Bypass for PHP provides a quick way to create a custom HTTP Server to return predefined responses to client requests.</p>
      <p>This is useful in tests when your application make requests to external services, and you need to simulate different situations like returning specific data or unexpected server errors.</p>
    </td>
  </tr>
</table>

-------

## Installation

📌 Bypass requires PHP 8.0+.

To install via [composer](https://getcomposer.org), run the following command:

```bash
composer require --dev ciareis/bypass
```

-------

## Writing Tests

### Content

- [Open Bypass Server](#1-open-a-bypass-server)
- [Bypass URL and Port](#2-bypass-url-and-port)
- [Routes](#3-routes)
    - [Standard Route](#31-standard-route)
    - [File Route](#32-file-route)
    - [Bypass Serve and Route Helpers](#33-bypass-serve-and-route-helpers)
- [Assert Route](#4-asserting-route-calling)
- [Stop or shut down](#5-stop-or-shut-down)

📝 Note: If you wish to view full codes, head to the [Examples](#examples) section.

### 1. Open a Bypass Server

To write a test, first open a Bypass server:

```php
//Opens a new Bypass server
$bypass = Bypass::open();
```

Bypass will always run at `http://localhost` listening to a random port number.

If needed, a port can be specified passing it as an argument `(int) $port`:

```php
//Opens a new Bypass using port 8081
$bypass = Bypass::open(8081);
```

### 2. Bypass URL and Port

The Bypass server URL can be retrieved with `getBaseUrl()`:

```php
$bypassUrl = $bypass->getBaseUrl(); //http://localhost:16819
```

If you need to retrieve only the port number, use the `getPort()` method:

```php
$bypassPort = $bypass->getPort(); //16819
```

### 3. Routes

Bypass serves two types of routes: The `Standard Route`, which can return a text body content and the `File Route`, which returns a binary file.

When running your tests, you will inform Bypass routes to Application or Service, making it access Bypass URLs instead of the real-world URLs.

#### 3.1 Standard Route

```php
use Ciareis\Bypass\Bypass;

//Json body
$body = '{"username": "john", "name": "John Smith", "total": 1250}';

//Route retuning the JSON body with HTTP Status 200
$bypass->addRoute(method: 'GET', uri: '/v1/demo/john', status: 200, body: $body);

//Instantiates a DemoService class
$service = new DemoService();

//Consumes the service using the Bypass URL
$response = $service->setBaseUrl($bypass->getBaseUrl())
  ->getTotalByUser('john');

//Your test assertions here...
```

The method `addRoute()` accepts the following parameters:

| Parameter       | Type                  | Description                                                                                                         |
| :-------------- | :-------------------- | :------------------------------------------------------------------------------------------------------------------ |
| **HTTP Method** | `string $method`      | [HTTP Request Method](https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html) (GET/POST/PUT/PATCH/DELETE)           |
| **URI**         | `string $uri`         | URI to be served by Bypass                                                                                          |
| **Status**      | `int $status`         | [HTTP Status Code](https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html) to be returned by Bypass (default: 200) |
| **Body**        | `string\|array $body` | Body to be served by Bypass (optional)                                                                              |
| **Times**       | `int $times`          | How many times the route should be called (default: 1)                                                              |

#### 3.2 File Route

```php
use Ciareis\Bypass\Bypass;

//Reads a PDF file
$demoFile = \file_get_contents('storage/pdfs/demo.pdf');

//File Route returning a binary file with HTTP Status 200
$bypass->addFileRoute(method: 'GET', uri: '/v1/myfile', status: 200, file: $demoFile);

//Instantiates a DemoService class
$service = new DemoService();

//Consumes the service using the Bypass URL
$response = $service->setBaseUrl($bypass->getBaseUrl())
  ->getPdf();

//Your test assertions here...
```

The method `addFileRoute()` accepts the following parameters:

| Parameter       | Type             | Description                                                                                                         |
| :-------------- | :--------------- | :------------------------------------------------------------------------------------------------------------------ |
| **HTTP Method** | `string $method` | [HTTP Request Method](https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html) (GET/POST/PUT/PATCH/DELETE)           |
| **URI**         | `string $uri`    | URI to be served by Bypass                                                                                          |
| **Status**      | `int $status`    | [HTTP Status Code](https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html) to be returned by Bypass (default: 200) |
| **File**        | `binary $file`   | Binary file to be served by Bypass                                                                                  |
| **Times**       | `int $times`     | How many times the route should be called (default: 1)                                                              |

#### 3.3 Bypass Serve and Route Helpers

Bypass comes with a variety of convenient shortcuts to the most-common-used HTTP requests.

These shortcuts are called "Route Helpers" and are served automatically at a random port using `Bypass::serve()`.

When serving Route Helpers, there is no need to call `Bypass::open()`.

Example:

```php
use Ciareis\Bypass\Bypass;
use Ciareis\Bypass\Route;

//Create and serve routes
$bypass = Bypass::serve(
  Route::ok(uri: '/v1/demo/john', body: ['username' => 'john', 'name' => 'John Smith', 'total' => 1250]), //method GET, status 200
  Route::notFound(uri: '/v1/demo/wally') //method GET, status 404
);

//Instantiates a DemoService class
$service = new DemoService();
$service->setBaseUrl($bypass->getBaseUrl());

//Consumes the "OK (200)" route
$responseOk = $service->getTotalByUser('john'); //200 - OK with total => 1250

//Consumes the "Not Found (404)" route
$responseNotFound = $service->getTotalByUser('wally'); //404 - Not found

//Your test assertions here...
```

In the example above Bypasss serves two routes: A URL accessible by method `GET` returning a JSON body with status `200`, and a second route URL accessible by method `GET` and returning status `404`.

#### Route Helpers

| Route Helper              | Default Method | HTTP Status    | Body                     | Common usage                      |
| :------------------------ | :------------- | :------------- | :----------------------- | :-------------------------------- |
| **Route::ok()**           | GET            | 200            | optional (string\|array) | Request was successful            |
| **Route::created()**      | POST           | 201            | optional (string\|array) | Response to a POST request which resulted in a creation |
| **Route::badRequest()**   | POST           | 400            | optional (string\|array) | Something can't be parsed (ex: wrong parameter) |
| **Route::unauthorized()** | GET            | 401            | optional (string\|array) | Not logged in                     |
| **Route::forbidden()**    | GET            | 403            | optional (string\|array) | Logged in but trying to request a restricted resource (without permission) |
| **Route::notFound()**     | GET            | 404            | optional (string\|array) | URL or resource does not exist    |
| **Route::notAllowed()**   | GET            | 405            | optional (string\|array) | Method not allowed                |
| **Route::validationFailed()** | POST       | 422            | optional (string\|array) | Data sent does not satisfy validation rules |
| **Route::tooMany()**      | GET            | 429            | optional (string\|array) | Request rejected due to server limitation |
| **Route::serverError()**  | GET            | 500            | optional (string\|array) | General indication that something is wrong on the server side |

You may also adjust the helpers to your needs by passing parameters:
  
| Parameter       | Type             | Description                                                                                                         |
| :-------------- | :--------------- | :------------------------------------------------------------------------------------------------------------------ |
| **URI**         | `string $uri`    | URI to be served by Bypass                                                                                          |
| **Body**        | `string\|array $body` | Body to be served by Bypass (optional)                                                                              |
| **HTTP Method** | `string $method` | [HTTP Request Method](https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html) (GET/POST/PUT/PATCH/DELETE)           |
| **Times**       | `int $times`     | How many times the route should be called (default: 1)                                                              |

In the example below, you can see the Helper `Route::badRequest` using method `GET` instead of its default method `POST`.

```php
use Ciareis\Bypass\Bypass;
use Ciareis\Bypass\Route;

Bypass::serve(
  Route::badRequest(uri: '/v1/users?filter=foo', body: ['error' => 'Filter parameter foo is not allowed.'], method: 'GET')
);
```

📝 Note: Custom routes can be created using a [Standard Route](#31-standard-route) in case something you need is not covered by the Helpers.

### 4. Asserting Route Calling

You may need to assert that a route was called at least one or multiple times.

The method `assertRoutes()` will return a `RouteNotCalledException` if a route was NOT called as many times as defined in the `$times` parameter.

If you need to assert that a route is NOT being called by your service, set the parameter `$times = 0`

```php
//Json body
$body = '{"username": "john", "name": "John Smith", "total": 1250}';

//Defines a route which must be called two times
$bypass->addRoute(method: 'GET', uri: '/v1/demo/john', status: 200, body: $body, times: 2);

//Instantiates a DemoService class
$service = new DemoService();

//Consumes the service using the Bypass URL
$response = $service->setBaseUrl($bypass->getBaseUrl())
  ->getTotalByUser('john');

$bypass->assertRoutes();

//Your test assertions here...
```

### 5. Stop or shut down

Bypass will automatically stop its server once your test is done running.

The Bypass server can be stopped or shut down at any point with the following methods:

To stop:
`$bypass->stop();`

To shut down:
`$bypass->down();`

## Examples

### Use case

To better illustrate Bypass usage, imagine you need to write a test for a service called `TotalScoreService`. This service calculates the total game score of a given username.
To get the score is obtained making external request to a fictitious API at `emtudo-games.com/v1/score/::USERNAME::`. The API returns HTTP Status `200` and a JSON body with a list of games:

```json
{
  "games": [
    {
      "id": 1,
      "points": 25
    },
    {
      "id": 2,
      "points": 10
    }
  ],
  "is_active": true
}
```

```php
use Ciareis\Bypass\Bypass;

//Opens a new Bypass server
$bypass = Bypass::open();

//Retrieves the Bypass URL
$bypassUrl = $bypass->getBaseUrl();

//Json body
$body = '{"games":[{"id":1, "name":"game 1","points":25},{"id":2, "name":"game 2","points":10}],"is_active":true}';

//Defines a route
$bypass->addRoute(method: 'GET', uri: '/v1/score/johndoe', status: 200, body: $body);

//Instantiates a TotalScoreService
$service = new TotalScoreService();

//Consumes the service using the Bypass URL
$response = $serivce
  ->setBaseUrl($bypassUrl) // set the URL to the Bypass URL
  ->getTotalScoreByUsername('johndoe'); //returns 35

//Pest PHP verify that response is 35
expect($response)->toBe(35);

//PHPUnit verify that response is 35
$this->assertSame($response, 35);
```

### Quick Test Examples

Click below to see code snippets for [Pest PHP](https://pestphp.com) and PHPUnit.

<details><summary>Pest PHP</summary>

```php
use Ciareis\Bypass\Bypass;


it('properly returns the total score by username', function () {

  //Opens a new Bypass server
  $bypass = Bypass::open();

  //Json body
  $body = '{"games":[{"id":1, "name":"game 1","points":25},{"id":2, "name":"game 2","points":10}],"is_active":true}';

  //Defines a route
  $bypass->addRoute(method: 'GET', uri: '/v1/score/johndoe', status: 200, body: $body);

  //Instantiates and consumes the service using the Bypass URL
  $service = new TotalScoreService();
  $response = $service
    ->setBaseUrl($bypass->getBaseUrl())
    ->getTotalScoreByUsername('johndoe');

  //Verifies that response is 35
  expect($response)->toBe(35);
});

it('properly gets the logo', function () {

  //Opens a new Bypass server
  $bypass = Bypass::open();

  //Reads the file
  $filePath = 'docs/img/logo.png';
  $file = \file_get_contents($filePath);

  //Defines a route
  $bypass->addFileRoute(method: 'GET', uri: $filePath, status: 200, file: $file);

  //Instantiates and consumes the service using the Bypass URL
  $service = new LogoService();
  $response = $service->setBaseUrl($bypass->getBaseUrl())
    ->getLogo();

  // asserts
  expect($response)->toEqual($file);
});
```

</details>

<details><summary>PHPUnit</summary>

```php
use Ciareis\Bypass\Bypass;


class BypassTest extends TestCase
{
  public function test_total_score_by_username(): void
  {
    //Opens a new Bypass server
    $bypass = Bypass::open();

    //Json body
    $body = '{"games":[{"id":1,"name":"game 1","points":25},{"id":2,"name":"game 2","points":10}],"is_active":true}';

    //Defines a route
    $bypass->addRoute(method: 'GET', uri: '/v1/score/johndoe', status: 200, body: $body);

    //Instantiates and consumes the service using the Bypass URL
    $service = new TotalScoreService();
    $response = $service
      ->setBaseUrl($bypass->getBaseUrl())
      ->getTotalScoreByUsername('johndoe');

    //Verifies that response is 35
    $this->assertSame(35, $response);
  }

  public function test_gets_logo(): void
  {
    //Opens a new Bypass server
    $bypass = Bypass::open();

    //Reads the file
    $filePath = 'docs/img/logo.png';
    $file = \file_get_contents($filePath);

    //Defines a route
    $bypass->addFileRoute(method: 'GET', uri: $filePath, status: 200, file: $file);

    //Instantiates and consumes the service using the Bypass URL
    $service = new LogoService();
    $response = $service->setBaseUrl($bypass->getBaseUrl())
      ->getLogo();

    $this->assertSame($response, $file);
  }
}
```

</details>

### Test Examples

📚 See Bypass being used in complete tests with [Pest PHP](https://github.com/ciareis/bypass/blob/main/tests/BypassPestTest.php) and [PHPUnit](https://github.com/ciareis/bypass/blob/main/tests/BypassPhpUnitTest.php) for the [GithubRepoService](https://github.com/ciareis/bypass/blob/main/tests/Services/GithubRepoService.php) demo service.

## Credits

- [Leandro Henrique](https://github.com/emtudo)
- [All Contributors](../../contributors)

And a special thanks to [@DanSysAnalyst](https://github.com/dansysanalyst)

### Inspired

Code inspired by [Bypass](https://github.com/PSPDFKit-labs/bypass)
