<?php

declare(strict_types=1);

namespace App;

class StudentDto
{
    public function __construct(
        public ?string $firstName,
        public ?string $lastName,
        public ?int $age,
        public ?string $studentCode,
    ) {
    }
}
