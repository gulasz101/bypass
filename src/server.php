<?php
declare(strict_types=1);

include_once("lib/functions.php");

if ($_SERVER['REQUEST_METHOD'] === "GET" && $_SERVER['PHP_SELF'] === '/___api_faker_router_index') {
    $route = getRoute($_GET['route'], $_GET['method']);
    $route = json_decode($route, true, JSON_THROW_ON_ERROR);

    echo $route['count'];

    exit;
}


if ($_SERVER['REQUEST_METHOD'] === "PUT" && $_SERVER['REQUEST_URI'] === '/___api_faker_clear_router') {
    foreach (glob('*.bypass') as $file) {
        unlink($file);
    }
    echo "ok.";
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === "PUT" && $_SERVER['REQUEST_URI'] === '/___api_faker_add_router') {
    $inputs = file_get_contents("php://input");
    $router = json_decode($inputs, true, JSON_THROW_ON_ERROR);

    setRoute($router['uri'], $router['method'], $router);
    http_response_code(200);

    echo "ok.";
    exit;
}

if ($route = currentRoute()) {
    $route = json_decode($route, true, JSON_THROW_ON_ERROR);

    http_response_code($route['status']);
    setRoute($route['uri'], $route['method'], $route);

    if (($route['file'] !== null)) {
        echo base64_decode($route['file']);

        exit;
    }

    echo $route['content'];

    exit;
}

http_response_code(404);
$route = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
echo "Bypass route {$route} and method {$method} not found.";
