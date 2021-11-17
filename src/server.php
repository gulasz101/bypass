<?php

declare(strict_types=1);

include __DIR__ . '/../vendor/autoload.php';

$routes = null;
$resetCounter = 1;

$resetRoutesFile = function ($anyComment = null) use (&$routes, &$resetCounter) {
    if (null !== $routes) {
//        rewind($routes);
//        $anyComment = $anyComment ?: $resetCounter;
//        file_put_contents(
//            "resetRoutesFile_{$anyComment}_{$resetCounter}.json",
//            stream_get_contents($routes)
//            . PHP_EOL . PHP_EOL
//            . print_r(debug_backtrace(), true)
//            . PHP_EOL . PHP_EOL
//        );
        $resetCounter++;
    }
    $routes = null;
    $routes = tmpfile();
    fwrite($routes, json_encode([]));
};
$resetRoutesFile();

$fileWriter = function (array $route) use ($routes): bool {
    rewind($routes);

    $streamContents = stream_get_contents($routes);
    $decodedRoutes = json_decode($streamContents, true, JSON_THROW_ON_ERROR);
    $decodedRoutes[] = $route;

//    rewind($routes);
    return !fwrite($routes, json_encode($decodedRoutes));
};

$fileReader = function () use ($routes): array {
    rewind($routes);
file_put_contents('file_reader',file_get_contents('file_reader') . PHP_EOL . PHP_EOL .  stream_get_contents($routes));
    return json_decode(stream_get_contents($routes), true, JSON_THROW_ON_ERROR);
};

$http = new \React\Http\HttpServer(
    function (\Psr\Http\Message\ServerRequestInterface $request) use ($resetRoutesFile, $fileWriter, $fileReader, $routes) {
        sleep(10);
        try {
//            if (true/*$request->getMethod() === 'PUT'*/) {
//                rewind($routes);
//                file_put_contents(
//                    'handle',
//                    file_get_contents('handle') .
//                    print_r(
//                        [
//                            $request->getMethod(),
//                            $request->getUri()->getPath(),
//                            (string)$request->getBody(),
//                            getmypid(),
//                            stream_get_contents($routes)
//                        ],
//                        true
//                    ) . PHP_EOL . PHP_EOL
//                );
//            }
            if ($request->getMethod() === 'PUT' && $request->getUri()->getPath() === '/___api_faker_add_router') {
//            $resetRoutesFile('___api_faker_add_router');
                $router = json_decode((string)$request->getBody(), true, JSON_THROW_ON_ERROR);
                setRoute($router['uri'], $router['method'], $router, $fileWriter);
                return new \React\Http\Message\Response(
                    200,
                    ['Content-Type' => 'text/plain'],
                    "ok."
                );
            }

            if ($request->getMethod() === 'PUT' && $request->getUri()->getPath() === '/___api_faker_clear_router') {
                $resetRoutesFile('___api_faker_clear_router');

                return new \React\Http\Message\Response(
                    200,
                    ['Content-Type' => 'text/plain'],
                    "ok."
                );
            }

            $allRoutes = $fileReader();
        } catch (\Throwable $jsonException) {
            file_put_contents(
                'jsonException',
//                file_get_contents('jsonException') .
                print_r($jsonException, true) .
                PHP_EOL . PHP_EOL
            );
        }
//        $allRoutes = $fileReader();
//        dd([]);
//        $filteredRoutes = array_filter(
//            $allRoutes,
//            fn (array $route) =>
//                $route['uri'] === $request->getUri()->getPath()
//                && $route['method'] === $request->getMethod()
//        );
//
//        if (!empty($filteredRoutes)) {
//            $route = $filteredRoutes[0];
//
//            return new \React\Http\Message\Response(
//                $route['status'],
//                [],
//                $route['file'] ? base64_decode($route['file']) : $route['content'],
//            );
//        }

        return new \React\Http\Message\Response(
            404,
            ['Content-Type' => 'text/plain'],
            "Bypass route {$request->getUri()->getPath()} and method {$request->getMethod()} not found.",
        );
    }
);

$socket = new \React\Socket\SocketServer('0.0.0.0:10880');
$http->listen($socket);

//echo '0.0.0.0:10880 started';

//function dump_to_file(...$data)
//{
//    $data = [...$data, $_SERVER];
//    file_put_contents('routes', file_get_contents('routes') . PHP_EOL . PHP_EOL . print_r($data, true));
//}
/**
 * @throws \JsonException
 */
//function writeFile($filename, $content) use ($routes): bool
//{
//    fseek($routes, 0);
//    return !file_put_contents($filename, json_encode($content, JSON_THROW_ON_ERROR));
//}

//function getFilename($route, $method): string
//{
//    $sessionName = getSessionName();
//
//    $method = strtoupper($method);
//    $route = md5($route);
//
//    return "{$sessionName}_{$method}_{$route}.bypass";
//}

//function getSessionName(): string
//{
//    $sessionName = getenv()['SESSION_NAME'] ?? throw new RuntimeException('env SESSION_NAME not set!');
//
//    return str_replace('.', '_', $sessionName);
//}

//function getRoute(string $route, string $method = null, callable $fileReader): string
//{
//    $file = getFilename($route, $method);
//
//    if (!file_exists($file)) {
//        return "";
//    }
//
//    $fileContents = file_get_contents($file);
//
//    if (false === $fileContents) {
//        throw new \RuntimeException('Unable to read file.');
//    }
//
//    return $fileContents;
//}

function setRoute(string $route, string $method, array $value, callable $routeWriter): void
{
    $content = [
        'uri' => $route,
        'method' => $method,
        'status' => $value['status'],
        'content' => $value['content'] ?? null,
        'file' => $value['file'] ?? null,
        'count' => isset($value['count']) ? $value['count'] + 1 : 0
    ];

    $routeWriter($content);
}

//function currentRoute(): string
//{
//    return getRoute($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
//}

function checkServer(string $expectedMethod, ?string $expectedUri = null, ?string $expectedPhpSelf = null): bool
{
    if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== $expectedMethod) {
        return false;
    }

    if ($expectedUri && (!isset($_SERVER['REQUEST_URI']) || $_SERVER['REQUEST_URI'] !== $expectedUri)) {
        return false;
    }

    if ($expectedPhpSelf && (!isset($_SERVER['PHP_SELF']) || $_SERVER['PHP_SELF'] !== $expectedPhpSelf)) {
        return false;
    }

    return true;
}


//if (checkServer(expectedMethod: 'GET', expectedPhpSelf: '/___api_faker_router_index')) {
//    $route = getRoute($_GET['route'], $_GET['method']);
//    $route = json_decode($route, true, JSON_THROW_ON_ERROR);
//
//    echo $route['count'];
//
//    exit;
//}
//
//
//if (checkServer(expectedMethod: 'PUT', expectedUri: '/___api_faker_clear_router')) {
//    unset($routes);
//    echo "ok.";
//    exit;
//}
//
//
//if (checkServer(expectedMethod: 'PUT', expectedUri: '/___api_faker_add_router')) {
//    $inputs = file_get_contents("php://input");
//    $router = json_decode($inputs, true, JSON_THROW_ON_ERROR);
//
//    setRoute($router['uri'], $router['method'], $router, $fileWriter);
//    http_response_code(200);
//    echo "ok.";
//    exit;
////    dump_to_file('___api_faker_add_router', $inputs, $fileReader());
//}
//
//if (/*checkServer('*', )*/ true) {
//    $allRoutes = $fileReader();
//dump_to_file('allRoutes', $allRoutes);
//    $filteredRoutes = array_filter(
//        $allRoutes,
//        fn (array $route) =>
//            $route['uri'] === $_SERVER['REQUEST_URI']
//            && $route['method'] === $_SERVER['REQUEST_METHOD']
//    );
//
//    if (!empty($filteredRoutes)) {
//        $route = $filteredRoutes[0];
//
//        http_response_code($route['status']);
//        setRoute($route['uri'], $route['method'], $route, $fileWriter);
//
//        if (($route['file'] !== null)) {
//            echo base64_decode($route['file']);
//        } else {
//            echo $route['content'];
//        }
//    } else {
//        http_response_code(404);
//        $route = $_SERVER['REQUEST_URI'];
//        $method = $_SERVER['REQUEST_METHOD'];
//        echo "Bypass route {$route} and method {$method} not found.";
//    }
//}
