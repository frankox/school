<?php

declare(strict_types=1);

namespace App;

use App\Utils\JsonToClassConverter;
use InvalidArgumentException;

class CreateStudentRequest
{
    public function __construct(
        public ?string $firstName,
        public ?string $lastName,
        public ?int $age,
        public ?string $studentCode,
    ) {
    }

    public static function fromJson(string $json): self
    {
        return new self(
            firstName: null,
            lastName: null,
            age: null,
            studentCode: null,
        );
    }

}
