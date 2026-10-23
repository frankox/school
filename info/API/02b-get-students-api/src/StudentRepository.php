<?php

declare(strict_types=1);

namespace App;

require_once dirname(__DIR__) . '/public/api/StudentDto.php';

use App\StudentDto as StudentDto;
use LogicException;
use PDO;

final class StudentRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function retrieveStudentsYoungerThan(int|null $minAge): array
    {
        $query = 'SELECT * FROM students';
        if ($minAge !== null) {
            $query .= ' WHERE age > :min_age';
        }

        $stmt = $this->pdo->prepare($query);
        if ($minAge !== null) {
            $stmt->bindValue(':min_age', $minAge, PDO::PARAM_INT);
        }

        $stmt->execute();

        $result = [];

        foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[] = new StudentDto(
                firstName: $row['first_name'],
                lastName: $row['last_name'],
                age: $row['age'],
                studentCode: $row['student_code']
            );
        }

        return $result;
    }
}
