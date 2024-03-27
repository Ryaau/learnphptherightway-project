<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Attributes\Get;
use App\Contracts\EmailValidationService;

class CurlController
{

    public function __construct(
      protected EmailValidationService $emailValidationService
    ) {}

    #[Get('/curl')]
    public function index()
    {
        $email = 'а.com';

        $result = $this->emailValidationService->verify($email);

        dd($result);
    }

}
