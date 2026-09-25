<?php

declare(strict_types=1);

namespace App;

use LogicException;
use PDO;

final class StudentBatchImporter
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function import(string $json): int
    {
        // TODO:
        // 1. decode and validate the non-empty JSON list;
        // 2. create every StudentData before starting the transaction;
        // 3. begin, prepare once, execute for every student and commit;
        // 4. roll back and rethrow if any database operation fails.
        throw new LogicException('Batch import not implemented yet.');
    }
}
