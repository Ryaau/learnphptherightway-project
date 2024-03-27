<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Attributes\Get;
use App\Services\Emailable\EmailValidationService;

class CurlController
{

    public function __construct(
      protected EmailValidationService $emailValidationService
    ) {}

    #[Get('/curl')]
    public function index()
    {
        $email = 'a@bc.com';

        $result = $this->emailValidationService->verify($email);

        dd($result);
    }

}
