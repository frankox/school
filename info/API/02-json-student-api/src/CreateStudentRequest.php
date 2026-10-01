<?php

declare(strict_types=1);

namespace App;

use App\Utils\JsonToClassConverter;

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
        $data = JsonToClassConverter::convert($json, self::class);

        return new self(
            firstName: $data->firstName,
            lastName: $data->lastName,
            age: $data->age,
            studentCode: $data->studentCode,
        );
    }
}
