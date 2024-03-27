<?php

namespace App\Helpers\EmailValidation;

use App\Enums\Emailable\RequestStatus;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class RetryMiddlewareProvider
{
    public function getRetryMiddleware(int $maxRetriesCount): callable
    {
        return Middleware::retry(
          function (
            int $retries,
            RequestInterface $request,
            ?ResponseInterface $response = null,
            ?RuntimeException $exception = null
          ) use ($maxRetriesCount) {
              if ($retries >= $maxRetriesCount) {
                  return false;
              }

              if ($response
                && in_array(
                  $response->getStatusCode(),
                  RequestStatus::getRetryAllowedStatusValues()
                )) {
                  return true;
              }

              if ($exception instanceof ConnectException) {
                  return true;
              }

              return false;
          }
        );
    }
}