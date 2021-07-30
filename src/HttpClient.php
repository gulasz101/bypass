<?php

declare(strict_types=1);

namespace Ciareis\Bypass;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpClient\HttplugClient;

/**
 * Class HttpClient
 * @package Ciareis\Bypass
 */
class HttpClient
{
    protected array $headers = [];

    protected Psr17Factory $psr17Factory;

    protected HttplugClient $httpPlugClient;

    public function __construct()
    {
        $this->psr17Factory = new Psr17Factory();
        $this->httpPlugClient = new HttplugClient(\Symfony\Component\HttpClient\HttpClient::create());
    }

    public function withHeaders(array $headers): self
    {
        $self = new self();

        $self->headers = $headers;

        return $self;
    }

    public function get(string $path): ResponseInterface
    {
        $request = $this->psr17Factory->createRequest('GET', $path);
        $request = $this->requestWithHeaders($request);

        return $this->httpPlugClient->sendRequest($request);
    }

    public function put(string $path, array|string $body): ResponseInterface
    {
        $request = $this->psr17Factory->createRequest('PUT', $path);
        $request = $this->requestWithBody($request, $body);
        $request = $this->requestWithHeaders($request);

        return $this->httpPlugClient->sendRequest($request);
    }

    public function post(string $path, array $body = null): ResponseInterface
    {
        $request = $this->psr17Factory->createRequest('POST', $path);
        $request = $this->requestWithBody($request, $body);
        $request = $this->requestWithHeaders($request);

        return $this->httpPlugClient->sendRequest($request);
    }

    public function patch(string $path, array|string $body = null): ResponseInterface
    {
        $request = $this->psr17Factory->createRequest('PATCH', $path);
        $request = $this->requestWithBody($request, $body);
        $request = $this->requestWithHeaders($request);

        return $this->httpPlugClient->sendRequest($request);
    }

    protected function requestWithHeaders(RequestInterface $request): RequestInterface
    {
        foreach ($this->headers as $header => $value) {
            $request = $request->withHeader($header, $value);
        }

        return $request;
    }

    protected function requestWithBody(RequestInterface $request, array|string|null $body = null): RequestInterface
    {
        if ($body) {

            if (is_array($body)) {
                $body = json_encode($body, JSON_THROW_ON_ERROR);
            }

            $request = $request->withBody(
                $this->psr17Factory->createStream($body)
            );
        }

        return $request;
    }
}
