<?php

namespace App\Services\Emailable;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class EmailValidationService
{

    private string $baseUrl = 'https://api.emailable.com/v1/';

    public function __construct(protected string $apiKey) {}

    public function verify(string $email): array|false
    {
        $client = new Client([
          'base_url' => $this->baseUrl,
          'timeout'  => 5,
        ]);

        $params = [
          'api_key' => $this->apiKey,
          'email'   => $email,
        ];

        $url = $this->baseUrl.'verify';

        $promise = $client->getAsync($url, ['query' => $params])->then(
          $this->getResponseBody(...)
        );

        return $promise->wait();
    }

    private function getResponseBody(ResponseInterface $response): array|false
    {
        if ($response->getStatusCode() === Response::HTTP_OK) {
            return json_decode($response->getBody()->getContents(), true);
        }

        return false;
    }

}