<?php

namespace App\Services\Emailable;

use App\Contracts\EmailValidationService as EmailValidationServiceContract;
use App\Helpers\EmailValidation\RetryMiddlewareProvider;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

class EmailValidationService implements EmailValidationServiceContract
{

    private string $baseUrl = 'https://api.emailable.com/v1/';

    public function __construct(
      protected string $apiKey,
      protected RetryMiddlewareProvider $retryMiddlewareProvider
    ) {}

    public function verify(string $email): array
    {
        $stack = HandlerStack::create();

        $maxRetriesCount = 3;

        $stack->push(
          $this->retryMiddlewareProvider->getRetryMiddleware($maxRetriesCount)
        );

        $client = new Client([
          'base_url' => $this->baseUrl,
          'timeout'  => 5,
          'handler'  => $stack,
        ]);

        $params = [
          'api_key' => $this->apiKey,
          'email'   => $email,
        ];

        $url = $this->baseUrl.'verify';

        $response = $client->get($url, ['query' => $params]);

        return json_decode(
          $response->getBody()->getContents(),
          true
        );
    }

}