<?php

declare(strict_types=1);

namespace Ciareis\Bypass;

use Psr\Http\Message\ResponseInterface;
use Ciareis\Bypass\HttpClient;

/**
 * @method static HttpClient withHeaders(array $headers)
 * @method static ResponseInterface get(string $path)
 * @method static ResponseInterface put(string $path, array|string $body)
 * @method static ResponseInterface patch(string $path, array|string $body = null)
 * @method static ResponseInterface post(string $path, array $body = null)
 */
class Http
{

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $parameters)
    {
        return (new HttpClient())->$method(...$parameters);
    }
}
