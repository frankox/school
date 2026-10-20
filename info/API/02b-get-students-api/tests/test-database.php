<?php

declare(strict_types=1);

// Each HTTP request gets a fresh in-memory database with the exercise fixtures.
return static function (): PDO {
    $pdo = new PDO('sqlite::memory:', null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pdo->exec(<<<'SQL'
        CREATE TABLE students (
            id INTEGER PRIMARY KEY,
            first_name TEXT NOT NULL,
            last_name TEXT NOT NULL,
            age INTEGER NOT NULL,
            student_code TEXT NOT NULL UNIQUE
        );

        INSERT INTO students (id, first_name, last_name, age, student_code) VALUES
            (1, 'Giulia', 'Bianchi', 16, 'ST-001'),
            (2, 'Marco', 'Rossi', 17, 'ST-002'),
            (3, 'Sara', 'Verdi', 18, 'ST-003'),
            (4, 'Luca', 'Neri', 18, 'ST-004'),
            (5, 'Amina', 'Conti', 19, 'ST-005'),
            (6, 'Paolo', 'Gallo', 21, 'ST-006');
        SQL);

    return $pdo;
};
