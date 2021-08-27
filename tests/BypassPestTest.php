<?php

/*
|--------------------------------------------------------------------------
| Pest  example
|--------------------------------------------------------------------------
|
| You can see Bypass being used in test written with Pest PHP
|
*/

use Ciareis\Bypass\Bypass;
use Ciareis\Bypass\Http;

use Ciareis\Bypass\RouteNotCalledException;

it('returns route not called exception', function () {
    // prepare

    $path = '/users/emtudo/repos';

    $this->bypass->addRoute(method: 'get', uri: $path, status: 503);
    $this->bypass->assertRoutes();
})->throws(RouteNotCalledException::class, "Bypass expected route '/users/emtudo/repos' with method 'GET' to be called 1 times(s). Found 0 calls(s) instead.");


it('returns route not found', function () {
    // prepare
    $this->bypass->addRoute(method: 'get', uri: '/no-route', status: 200);
    $this->bypass->clearOldRoutes();

    $response = Http::get($this->bypass->getBaseUrl('/no-route'));

    expect($response->getStatusCode())->toEqual(404);
    expect((string)$response->getBody())->toEqual('Bypass route /no-route and method GET not found.');
});

it('returns exceptions when server down', function () {
    // prepare
    $this->bypass->down();

    Http::get($this->bypass->getBaseUrl('/no-route'))->getBody();

})->throws(\Http\Client\Exception\NetworkException::class);

function getBody()
{
    return [
    [
    "stargazers_count" => 0
    ],
    [
    "stargazers_count" => 3
    ],
    [
    "stargazers_count" => 0,
    ],
    [
    "stargazers_count" => 1,
    ],
    [
    "stargazers_count" => 0,
    ],
    [
    "stargazers_count" => 0,
    ],
    [
    "stargazers_count" => 0,
    ],
    [
    "stargazers_count" => 0,
    ],
    [
    "stargazers_count" => 1,
    ],
    [
    "stargazers_count" => 0,
    ],
    [
    "stargazers_count" => 0,
    ],
    [
    "stargazers_count" => 0,
    ],
    [
    "stargazers_count" => 0,
    ],
    [
    "stargazers_count" => 1,
    ],
    [
    "stargazers_count" => 0,
    ],
    [
    "stargazers_count" => 2,
    ],
    [
    "stargazers_count" => 0,
    ],
    [
    "stargazers_count" => 0,
    ],
    [
    "stargazers_count" => 0,
    ],
    [
    "stargazers_count" => 4,
    ],
    [
    "stargazers_count" => 1,
    ],
    [
    "stargazers_count" => 0,
    ],
    [
    "stargazers_count" => 0,
    ],
    [
    "stargazers_count" => 1,
    ],
    [
    "stargazers_count" => 2,
    ],
    [
    "stargazers_count" => 0,
    ],
    [
    "stargazers_count" => 0,
    ],
    [
    "stargazers_count" => 0,
    ],
    [
    "stargazers_count" => 0,
    ],
    [
    "stargazers_count" => 0,
    ],
    ];
}
