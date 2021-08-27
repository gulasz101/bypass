<?php

use Ciareis\Bypass\Bypass;
use Ciareis\Bypass\Route;
use Ciareis\Bypass\RouteNotCalledException;
use Ciareis\Bypass\Http;
use Illuminate\Http\Client\RequestException;

it(
    'returns bypass with Bypass::serve',
    function () {
        $this->bypass = Bypass::serve(
            Route::ok(uri: '/v1/user'),
            Route::forbidden(uri: '/v1/user/1/secrets')
        );

        expect($this->bypass)->toBeInstanceOf(Bypass::class);
        expect($this->bypass->getRoutes())->toHaveCount(2);
        expect($this->bypass->getPort())->toBeInt();
        expect((string)$this->bypass)->toEqual($this->bypass->getBaseUrl());
    }
);

test(
    'Route::ok returns 200 + body',
    function () {
        $uri = '/v1/user';

        $this->bypass = bypass::serve(
            Route::ok(uri: $uri, body: ['name' => 'Leandro Henrique'])
        );

        $response = Http::get($this->bypass . $uri);

        expect((string)$response->getBody())
            ->json()
            ->name->toBe('Leandro Henrique');
        expect($response->getStatusCode())
            ->toBeInt()
            ->ToBe(200);
    }
);

test(
    'Route::created returns 201 + body',
    function () {
        $uri = '/v1/user';

        $this->bypass = bypass::serve(
            Route::created(uri: $uri, body: ['result' => 'User successfully created'])
        );

        $response = Http::post($this->bypass . $uri);

        expect((string)$response->getBody())
            ->json()
            ->result->toBe('User successfully created');

        expect($response->getStatusCode())->toBeInt()->ToBe(201);
    }
);

test(
    'Route::badRequest returns 400 + body',
    function () {
        $uri = '/v1/users?filter=foo';

        $this->bypass = bypass::serve(
            Route::badRequest(uri: $uri, body: ['error' => 'Filter parameter foo does not exist.'], method: 'GET')
        );

        $response = Http::get($this->bypass . $uri);
        expect((string)$response->getBody())
            ->json()
            ->error->toBe('Filter parameter foo does not exist.');
    }
);

test(
    'Route::Unauthorized returns 401 + body',
    function () {
        $uri = '/v1/my-favorites';

        $this->bypass = bypass::serve(
            Route::Unauthorized(uri: $uri, body: ['error' => 'Unauthenticated'])
        );

        $response = Http::get($this->bypass . $uri);

        expect((string)$response->getBody())
            ->json()
            ->error->toBe('Unauthenticated');
        expect($response->getStatusCode())->toBe(401);
    }
);


test(
    'Route::forbidden returns 403',
    function () {
        $uri = '/v1/user/1';

        $this->bypass = bypass::serve(
            Route::forbidden(uri: $uri, body: ['email' => 'leandro.new@ciareis.com'], method: 'PATCH')
        );

        expect(Http::patch($this->bypass . $uri))
            ->getStatusCode()->toBe(403);
    }
);


test(
    'Route::notFound returns 404',
    function () {
        $uri = '/v1/fruits';

        $this->bypass = bypass::serve(
            Route::notFound(uri: $uri)
        );

        expect(Http::get($this->bypass . $uri))
            ->getStatusCode()->toBe(404);;
    }
);


test(
    'Route::notAllowed returns 405',
    function () {
        $uri = '/update-user-with-get-method';

        $this->bypass = bypass::serve(
            Route::notAllowed(uri: $uri)
        );

        expect(Http::get($this->bypass . $uri))
            ->getStatusCode()->toBe(405);
    }
);

test(
    'Route::serverError returns 500 + body',
    function () {
        $uri = '/v1/foobar';

        $this->bypass = bypass::serve(
            Route::serverError(uri: $uri)
        );

        $response = Http::get($this->bypass . $uri);

        expect((string)$response->getBody())->toBeEmpty();
        expect($response->getStatusCode())->toBe(500);
    }
);

test(
    'Route::validationFailed returns 422 + body',
    function () {
        $uri = '/v1/user';

        $this->bypass = bypass::serve(
            Route::validationFailed(
                uri: $uri,
                body: ['validation_error' => ['first_name' => ['Name must be at least 5 characters long.']]]
            )
        );

        $response = Http::post($this->bypass . $uri);

        expect((string)$response->getBody())
            ->toBeJson()
            ->json()
            ->validation_error
            ->first_name
            ->toHaveKey('0', 'Name must be at least 5 characters long.');
    }
);

test(
    "Bypass->assertRotues no called /v1/login when it is first argument",
    function () {
        $body = "teste";
        $this->bypass = Bypass::serve(
            Route::ok(uri: '/v1/phone/teste', body: $body),
            Route::notFound(uri: '/v1/login', body: ['fruta' => 'banana']),
        );

        $response = Http::get($this->bypass->getBaseUrl('/v1/phone/teste'));

        expect((string)$response->getBody())->toEqual($body);

        $this->bypass->assertRoutes();
    }
)->throws(
    RouteNotCalledException::class,
    "Bypass expected route '/v1/login' with method 'GET' to be called 1 times(s). Found 0 calls(s) instead."
);

test(
    "Bypass->assertRotues no called /v1/login when it is second argument",
    function () {
        $body = "teste";
        $this->bypass = Bypass::serve(
            Route::notFound(uri: '/v1/login', body: ['fruta' => 'banana']),
            Route::ok(uri: '/v1/phone/teste', body: $body),
        );

        $response = Http::get($this->bypass->getBaseUrl('/v1/phone/teste'));

        expect((string)$response->getBody())->toEqual($body);

        $this->bypass->assertRoutes();
    }
)->throws(
    RouteNotCalledException::class,
    "Bypass expected route '/v1/login' with method 'GET' to be called 1 times(s). Found 0 calls(s) instead."
);

test(
    "Bypass->assertRotues no called /v1/phone/teste when it is first argument",
    function () {
        $body = ['fruta' => 'banana'];
        $this->bypass = Bypass::serve(
            Route::ok(uri: '/v1/phone/teste', body: 'teste'),
            Route::notFound(uri: '/v1/login', body: $body),
        );

        $response = Http::get($this->bypass->getBaseUrl('/v1/login'));

        expect((string)$response->getBody())->json()->toHaveKeys(['fruta']);
        expect((string)$response->getBody())->json()->toEqual($body);

        $this->bypass->assertRoutes();
    }
)->throws(
    RouteNotCalledException::class,
    "Bypass expected route '/v1/phone/teste' with method 'GET' to be called 1 times(s). Found 0 calls(s) instead."
);

test(
    "Bypass->assertRotues no called /v1/phone/teste when it is second argument",
    function () {
        $body = ['fruta' => 'banana'];
        $this->bypass = Bypass::serve(
            Route::notFound(uri: '/v1/login', body: $body),
            Route::ok(uri: '/v1/phone/teste', body: 'teste'),
        );

        $response = Http::get($this->bypass->getBaseUrl('/v1/login'));
        expect((string)$response->getBody())->json()->toHaveKeys(['fruta']);
        expect((string)$response->getBody())->json()->fruta->toBe('banana');

        $this->bypass->assertRoutes();
    }
)->throws(
    RouteNotCalledException::class,
    "Bypass expected route '/v1/phone/teste' with method 'GET' to be called 1 times(s). Found 0 calls(s) instead."
);
