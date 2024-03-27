<?php

namespace App\Enums\Emailable;

enum RequestStatus: int
{

    case TryAgain = 249;
    case BadRequest = 400;
    case Unauthorized = 401;
    case PaymentRequired = 402;
    case Forbidden = 403;
    case NotFound = 404;
    case TooManyRequests = 429;
    case InternalServerError = 500;
    case ServiceUnavailable = 503;

    public static function getRetryAllowedStatusValues(): array
    {
        return array_map(fn(RequestStatus $status) => $status->value, [
          self::TryAgain,
          self::TooManyRequests,
          self::ServiceUnavailable,
        ]);
    }

}