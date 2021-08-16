<?php

declare(strict_types=1);

namespace Ciareis\Bypass;

use JetBrains\PhpStorm\Pure;

class Route
{
    public function __construct(
        public string $method,
        public string $uri,
        public int $status,
        public null|string|array $body = null,
        public int $times = 1
    ) {
    }

    #[Pure] public static function ok(string $uri, null|string|array $body = null, string $method = "GET", int $times = 1): self
    {
        return new static(method: $method, uri: $uri, status: 200, body: $body, times: $times);
    }

    #[Pure] public static function badRequest(string $uri, null|string|array $body = null, string $method = 'POST', int $times = 1): self
    {
        return new static(method: $method, uri: $uri, status: 400, body: $body, times: $times);
    }

    #[Pure] public static function unauthorized(string $uri, null|string|array $body = null, string $method = 'GET', int $times = 1): self
    {
        return new static(method: $method, uri: $uri, status: 401, body: $body, times: $times);
    }

    #[Pure] public static function forbidden(string $uri, null|string|array $body = null, string $method = 'GET', int $times = 1): self
    {
        return new static(method: $method, uri: $uri, status: 403, body: $body, times: $times);
    }

    #[Pure] public static function created(string $uri, null|string|array $body = null, int $times = 1): self
    {
        return new static(method: "POST", uri: $uri, status: 201, body: $body, times: $times);
    }

    #[Pure] public static function notFound(string $uri, null|string|array $body = null, string $method = 'GET', int $times = 1): self
    {
        return new static(method: $method, uri: $uri, status: 404, body: $body, times: $times);
    }

    #[Pure] public static function notAllowed(string $uri, null|string|array $body = null, string $method = 'GET', int $times = 1): self
    {
        return new static(method: $method, uri: $uri, status: 405, body: $body, times: $times);
    }

    #[Pure] public static function tooMany(string $uri, null|string|array $body = null, string $method = 'GET', int $times = 1): self
    {
        return new static(method: $method, uri: $uri, status: 429, body: $body, times: $times);
    }

    #[Pure] public static function serverError(string $uri, null|string|array $body = null, string $method = 'GET', int $times = 1): self
    {
        return new static(method: $method, uri: $uri, status: 500, body: $body, times: $times);
    }

    #[Pure] public static function validationFailed(string $uri, null|string|array $body = null, string $method = 'POST', int $times = 1): self
    {
        return new static(method: $method, uri: $uri, status: 422, body: $body, times: $times);
    }

    #[Pure] public static function file(string $uri, string $file, string $method = 'GET', int $status = 200, int $times = 1): self
    {
        return new RouteFile(method: $method, uri: $uri, file: $file, status: $status, times: $times);
    }

    #[Pure] public static function get(string $uri, null|string|array $body = null, int $status = 200, int $times = 1): self
    {
        return new static(method: "GET", uri: $uri, status: $status, body: $body, times: $times);
    }

    #[Pure] public static function getFile(string $uri, string $file, int $status = 200, int $times = 1): self
    {
        return new RouteFile(method: "GET", uri: $uri, file: $file, status: $status, times: $times);
    }

    #[Pure] public static function post(string $uri, null|string|array $body = null, int $status = 200, int $times = 1): self
    {
        return new static(method: "POST", uri: $uri, status: $status, body: $body, times: $times);
    }

    #[Pure] public static function put(string $uri, null|string|array $body = null, int $status = 200, int $times = 1): self
    {
        return new static(method: "PUT", uri: $uri, status: $status, body: $body, times: $times);
    }

    #[Pure] public static function delete(string $uri, null|string|array $body = null, int $status = 204, int $times = 1): self
    {
        return new static(method: "DELETE", uri: $uri, status: $status, body: $body, times: $times);
    }

    #[Pure] public static function patch(string $uri, null|string|array $body = null, int $status = 200, int $times = 1): self
    {
        return new static(method: "PATCH", uri: $uri, status: $status, body: $body, times: $times);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
