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
        // TODO: preparare ed eseguire una INSERT, poi restituire l'ID generato.
        throw new LogicException('Completa la creazione dello studente.');
    }
}
