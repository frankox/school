<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;

final class StudentData
{
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly int $age,
        public readonly string $studentCode,
    ) {
    }

    public static function fromArray(mixed $data): self
    {
        if (!is_array($data) || array_is_list($data)) {
            throw new InvalidArgumentException('Each student must be a JSON object.');
        }

        $required = ['age', 'first_name', 'last_name', 'student_code'];
        $actual = array_keys($data);
        sort($actual);

        if ($actual !== $required) {
            throw new InvalidArgumentException('Each student must contain exactly the required fields.');
        }

        foreach (['first_name', 'last_name', 'student_code'] as $field) {
            if (!is_string($data[$field]) || trim($data[$field]) === '') {
                throw new InvalidArgumentException("{$field} must be a non-empty string.");
            }
        }

        if (!is_int($data['age']) || $data['age'] < 14 || $data['age'] > 100) {
            throw new InvalidArgumentException('age must be an integer between 14 and 100.');
        }

        return new self(
            $data['first_name'],
            $data['last_name'],
            $data['age'],
            $data['student_code'],
        );
    }

    public function parameters(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'age' => $this->age,
            'student_code' => $this->studentCode,
        ];
    }
}
