<?php

declare(strict_types=1);

namespace App;

use LogicException;
use PDO;

final class StudentRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function create(CreateStudentRequest $request): int
    {
        // TODO: prepare and execute one INSERT, then return lastInsertId().
        throw new LogicException('Student persistence not implemented yet.');
    }
}
