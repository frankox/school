<?php

declare(strict_types=1);

namespace App;

use LogicException;

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
        // TODO: interpretare il JSON e validare tutti i campi richiesti.
        throw new LogicException('Completa la lettura e la validazione dello studente.');
    }
}
