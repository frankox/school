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
        $stmt = $this->prepareInsertStatement();
        $stmt->execute([
            ':first_name' => $request->firstName,
            ':last_name' => $request->lastName,
            ':age' => $request->age,
            ':student_code' => $request->studentCode,
        ]);
        return $this->pdo->lastInsertId();
    }

    private function prepareInsertStatement(): \PDOStatement
    {
        $sql = 'INSERT INTO students (first_name, last_name, age, student_code) VALUES (:first_name, :last_name, :age, :student_code)';
        return $this->pdo->prepare($sql);
    }
}
