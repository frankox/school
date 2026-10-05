<?php

declare(strict_types=1);

namespace App;

use LogicException;
use PDO;

final class StudentRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function create(CreateStudentRequest $request): string | false
    {
        // TODO: prepare and execute one INSERT, then return lastInsertId().
        return false;
    }
}
