<?php

namespace App\Contracts;

interface EmailValidationService
{
    public function verify(string $email): array;
}