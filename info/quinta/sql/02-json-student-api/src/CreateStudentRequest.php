<?php

declare(strict_types=1);

namespace App;

use LogicException;

final class CreateStudentRequest
{
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly int $age,
        public readonly string $studentCode,
    ) {
    }

    public static function fromJson(string $json): self
    {
        // TODO: decode and validate the JSON described in the README.
        throw new LogicException('JSON parsing not implemented yet.');
    }
}
