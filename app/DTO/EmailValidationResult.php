<?php

declare(strict_types = 1);

namespace App\DTO;

readonly class EmailValidationResult
{
    public function __construct(public int $score, public bool $isDeliverable)
    {
    }
}
