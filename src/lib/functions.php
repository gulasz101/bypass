<?php
declare(strict_types=1);

/**
 * @throws \JsonException
 */
function writeFile($filename, $content): bool
{
    return !file_put_contents($filename, json_encode($content, JSON_THROW_ON_ERROR));
}

function getFilename($route, $method): string
{
    $sessionName = getSessionName();

    $method = strtoupper($method);
    $route = md5($route);

    return "{$sessionName}_{$method}_{$route}.bypass";
}

function getSessionName(): string
{
    $sessionName = getenv()['SESSION_NAME'] ?? throw new RuntimeException('env SESSION_NAME not set!');

    return str_replace('.', '_', $sessionName);
}

function getRoute(string $route, string $method = null): string
{
    $file = getFilename($route, $method);

    if (!file_exists($file)) {
        return "";
    }

    $fileContents = file_get_contents($file);

    if (false === $fileContents) {
        throw new \RuntimeException('Unable to read file.');
    }

    return $fileContents;
}

function setRoute(string $route, string $method, array $value): void
{
    $file = getFilename($route, $method);

    $content = [
        'uri' => $route,
        'method' => $method,
        'status' => $value['status'],
        'content' => $value['content'] ?? null,
        'file' => $value['file'] ?? null,
        'count' => isset($value['count']) ? $value['count'] + 1 : 0
    ];

    writeFile($file, $content);
}

function currentRoute(): string
{
    return getRoute($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
}
