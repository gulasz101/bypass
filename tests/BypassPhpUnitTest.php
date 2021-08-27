<?php

/*
|--------------------------------------------------------------------------
| PHPUnit test example
|--------------------------------------------------------------------------
|
| You can see Bypass being used in a PHPUnit test
|
*/

declare(strict_types=1);

namespace Tests;

use Ciareis\Bypass\Bypass;
use Ciareis\Bypass\Http;
use Http\Client\Exception\NetworkException;

class BypassPhpUnitTest extends TestCase
{
    public function test_returns_route_not_found(): void
    {
        $this->bypass->addRoute(method: 'GET', uri: '/no-route',);
        $this->bypass->clearOldRoutes();

        expect(Http::get($this->bypass->getBaseUrl('/no-route')))
            ->getStatusCode()->toBe(404)
            ->getBody()
            ->__toString()
            ->toBe('Bypass route /no-route and method GET not found.');
    }

    public function test_returns_route_not_called_exception(): void
    {
        $path = '/users/emtudo/repos';

        $this->bypass->addRoute(method: 'GET', uri: $path, status: 503, times: 1);
        $this->expectException(\Ciareis\Bypass\RouteNotCalledException::class);

        $this->bypass->assertRoutes();
    }

    public function test_returns_exceptions_when_server_down(): void
    {
        $baseUrl = $this->bypass->getBaseUrl('/no-route');
        $this->bypass->down();

        $this->expectException(NetworkException::class);

        Http::get($baseUrl);
    }

    protected function getBody()
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
}
