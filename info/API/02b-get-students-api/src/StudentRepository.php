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
        // TODO: leggere gli studenti con PDO, applicare l'eventuale filtro
        //       min_age, ordinare per id e convertire le righe in StudentDto.
        throw new LogicException('Completa la lettura degli studenti.');
    }
}
