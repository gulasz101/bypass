<?php

namespace Tests\Services;

use Ciareis\Bypass\Http;
use Exception;

class GithubRepoService
{
    protected $baseUrl = "https://api.github.com";

    public function setBaseUrl(string $url)
    {
        $this->baseUrl = $url;

        return $this;
    }

    public function getTotalStargazersByUser(string $username)
    {
        $url = "{$this->baseUrl}/users/${username}/repos";

        try {
            $response = Http::get($url);
        } catch (Exception $e) {
            return "Server down.";
        }

        if ($response->getStatusCode() === 503) {
            return "Server unavailable.";
        }

        $data = json_decode((string)$response->getBody(), true, JSON_THROW_ON_ERROR);

        $sum = 0;
        foreach ($data as $datum) {
            $sum += $datum['stargazers_count'] ?? 0;
        }

        return $sum;
    }
}
