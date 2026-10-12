<?php

declare(strict_types=1);

// Simula le righe dello schema e controlla che la SELECT usi una proiezione
// esplicita e, quando serve, un parametro per la condizione WHERE.
final class StudentsStatement extends PDOStatement
{
    private array $parameters = [];

    public function __construct(private string $sql)
    {
    }

    public function execute(?array $params = null): bool
    {
        $this->parameters = $params ?? [];
        return true;
    }

    public function fetchAll(int $mode = PDO::FETCH_DEFAULT, mixed ...$args): array
    {
        if (!preg_match('/^\s*SELECT\s+(.+?)\s+FROM\s+students\b/is', $this->sql, $matches)) {
            throw new PDOException('La GET deve eseguire una SELECT dalla tabella students.');
        }
        if (!preg_match('/\bORDER\s+BY\s+id\b/i', $this->sql)) {
            throw new PDOException('Ordina gli studenti per id.');
        }

        $selected = array_map('trim', explode(',', strtolower($matches[1])));
        $allColumns = ['id', 'first_name', 'last_name', 'age', 'student_code'];
        if ($selected === ['*']) {
            $selected = $allColumns;
        }
        foreach ($selected as $column) {
            if (!in_array($column, $allColumns, true)) {
                throw new PDOException('Colonna non prevista nella SELECT: ' . $column);
            }
        }

        $minimum = null;
        if (stripos($this->sql, 'WHERE') !== false) {
            if (!preg_match('/\bWHERE\s+age\s*>=\s*:min_age\b/i', $this->sql)) {
                throw new PDOException('Usa WHERE age >= :min_age.');
            }
            $minimum = $this->parameters['min_age'] ?? $this->parameters[':min_age'] ?? null;
            if (!is_int($minimum) && !(is_string($minimum) && ctype_digit($minimum))) {
                throw new PDOException('Passa min_age a execute().');
            }
        }

        $rows = [
            ['id' => 1, 'first_name' => 'Giulia', 'last_name' => 'Bianchi', 'age' => 16, 'student_code' => 'ST-001'],
            ['id' => 2, 'first_name' => 'Marco', 'last_name' => 'Rossi', 'age' => 17, 'student_code' => 'ST-002'],
            ['id' => 3, 'first_name' => 'Sara', 'last_name' => 'Verdi', 'age' => 18, 'student_code' => 'ST-003'],
            ['id' => 4, 'first_name' => 'Luca', 'last_name' => 'Neri', 'age' => 18, 'student_code' => 'ST-004'],
            ['id' => 5, 'first_name' => 'Amina', 'last_name' => 'Conti', 'age' => 19, 'student_code' => 'ST-005'],
            ['id' => 6, 'first_name' => 'Paolo', 'last_name' => 'Gallo', 'age' => 21, 'student_code' => 'ST-006'],
        ];

        $result = [];
        foreach ($rows as $row) {
            if ($minimum !== null && $row['age'] < (int) $minimum) {
                continue;
            }
            $result[] = array_intersect_key($row, array_flip($selected));
        }
        return $result;
    }
}

final class StudentsPdo extends PDO
{
    public function __construct()
    {
    }

    public function prepare(string $query, array $options = []): PDOStatement|false
    {
        return new StudentsStatement($query);
    }
}

return static fn (): PDO => new StudentsPdo();
