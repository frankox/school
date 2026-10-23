<?php

declare(strict_types=1);

namespace App;

use App\Utils\JsonToClassConverter;

require_once __DIR__ . '/RequiredException.php';

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
        $data = JsonToClassConverter::convert($json, CreateStudentRequest::class);

        self::validateRequiredProperties($data);
        self::validateAge($data->age);

        return $data;
    }

    private static function isPropertyRequired(object $data, string $propertyName): bool
    {
        return isset($data->{$propertyName}) && trim((string)$data->{$propertyName}) !== '';
    }

    private static function validateRequiredProperties(object $data): void
    {
        foreach (['firstName', 'lastName', 'age', 'studentCode'] as $property) {
            if (!self::isPropertyRequired($data, $property)) {
                throw new RequiredException($property);
            }
        }
    }

    private static function validateAge(int $age): void
    {
        if ($age <= 14 || $age > 150) {
            throw new \InvalidArgumentException("The age must be between 15 and 150.");
        }
    }
}
