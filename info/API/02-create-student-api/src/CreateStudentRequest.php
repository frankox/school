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
        self::validate($this);
    }

    public static function fromJson(string $json): self
    {
        $data = JsonToClassConverter::convert($json, self::class);
        
        self::validate($data);

        return new self(
            firstName: $data->firstName,
            lastName: $data->lastName,
            age: $data->age,
            studentCode: $data->studentCode,
        );
    }

    private static function validate(self $instance): void
    {
        self::validateRequiredProperties($instance);
        self::validateAge($instance->age);
    }

    private static function validateRequiredProperties(self $instance): void
    {
        if (!isset($instance->firstName) || trim($instance->firstName) === '') {
            throw new InvalidArgumentException('firstName is required');
        }

        if (!isset($instance->lastName) || trim($instance->lastName) === '') {
            throw new InvalidArgumentException('lastName is required');
        }

        if (!isset($instance->age)) {
            throw new InvalidArgumentException('age is required');
        }

        if (!isset($instance->studentCode) || trim($instance->studentCode) === '') {
            throw new InvalidArgumentException('studentCode is required');
        }
    }

    private static function validateAge(int $age): void
    {
        if ($age < 14) {
            throw new InvalidArgumentException('age must be at least 14');
        }
    }
}
