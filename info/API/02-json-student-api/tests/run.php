<?php

declare(strict_types=1);

use App\CreateStudentRequest;
use App\StudentRepository;

require_once dirname(__DIR__) . '/src/CreateStudentRequest.php';
require_once dirname(__DIR__) . '/src/StudentRepository.php';
require_once dirname(__DIR__) . '/utils/JsonToClassConverter.php';

final class TestFailure extends RuntimeException
{
}

function assertSame(mixed $expected, mixed $actual): void
{
    if ($expected !== $actual) {
        throw new TestFailure('Expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
    }
}

function assertThrows(string $class, Closure $operation): void
{
    try {
        $operation();
    } catch (Throwable $exception) {
        if ($exception instanceof $class) {
            return;
        }

        $actualClass = $exception::class;
        throw new TestFailure("Expected {$class}, got {$actualClass}.");
    }

    throw new TestFailure("Expected {$class}, but none was thrown.");
}

// Queste due classi simulano PDO: registrano le chiamate senza aprire un database.
// Il test può così controllare separatamente SQL, valori e ID restituito.
final class RecordingStatement extends PDOStatement
{
    public ?array $parameters = null;

    public function __construct()
    {
    }

    public function execute(?array $params = null): bool
    {
        // PDO accetta i nomi dei parametri con o senza il prefisso ':'.
        $this->parameters = [];
        foreach ($params ?? [] as $name => $value) {
            $normalizedName = str_starts_with($name, ':') ? substr($name, 1) : $name;
            $this->parameters[$normalizedName] = $value;
        }
        return true;
    }
}

final class RecordingPdo extends PDO
{
    public ?string $sql = null;
    public RecordingStatement $statement;

    public function __construct()
    {
        $this->statement = new RecordingStatement();
    }

    public function prepare(string $query, array $options = []): PDOStatement|false
    {
        $this->sql = $query;
        return $this->statement;
    }

    public function lastInsertId(?string $name = null): string|false
    {
        return '42';
    }
}

$tests = [
    'creates a request from valid JSON and preserves leading zeros' => function (): void {
        $request = CreateStudentRequest::fromJson(
            '{"firstName":"Mario","lastName":"Rossi","age":18,"studentCode":"00123"}'
        );

        assertSame('Mario', $request->firstName);
        assertSame('Rossi', $request->lastName);
        assertSame(18, $request->age);
        assertSame('00123', $request->studentCode);
    },

    'rejects snake_case JSON names when camelCase is required' => function (): void {
        assertThrows(InvalidArgumentException::class, fn () => CreateStudentRequest::fromJson(
            '{"first_name":"Mario","last_name":"Rossi","age":18,"student_code":"00123"}'
        ));
    },

    'rejects malformed JSON' => function (): void {
        assertThrows(InvalidArgumentException::class, fn () => CreateStudentRequest::fromJson('{'));
    },

    'rejects a JSON array instead of an object' => function (): void {
        assertThrows(InvalidArgumentException::class, fn () => CreateStudentRequest::fromJson('[]'));
    },

    'rejects a missing studentCode field' => function (): void {
        assertThrows(
            InvalidArgumentException::class,
            fn () => CreateStudentRequest::fromJson(
                '{"firstName":"Mario","lastName":"Rossi","age":18}'
            )
        );
    },

    'rejects an empty firstName' => function (): void {
        assertThrows(
            InvalidArgumentException::class,
            fn () => CreateStudentRequest::fromJson(
                '{"firstName":"","lastName":"Rossi","age":18,"studentCode":"1"}'
            )
        );
    },

    'rejects age below 14' => function (): void {
        assertThrows(
            InvalidArgumentException::class,
            fn () => CreateStudentRequest::fromJson(
                '{"firstName":"Mario","lastName":"Rossi","age":12,"studentCode":"1"}'
            )
        );
    },

    // Passo 1: il repository definisce l'operazione SQL; non servono ancora i valori.
    'PDO step 1: prepares an INSERT with four placeholders' => function (): void {
        $pdo = new RecordingPdo();
        $request = new CreateStudentRequest('Mario', 'Rossi', 18, '00123');

        (new StudentRepository($pdo))->create($request);

        assertSame(
            'INSERT INTO students (first_name, last_name, age, student_code) '
                . 'VALUES (:first_name, :last_name, :age, :student_code)',
            $pdo->sql
        );
    },

    // Passo 2: execute riceve i valori, separati dal testo della query SQL.
    'PDO step 2: executes with values from the request' => function (): void {
        $pdo = new RecordingPdo();
        $request = new CreateStudentRequest('Mario', 'Rossi', 18, '00123');

        (new StudentRepository($pdo))->create($request);

        assertSame([
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'age' => 18,
            'student_code' => '00123',
        ], $pdo->statement->parameters);
    },

    // Passo 3: lastInsertId simula l'ID assegnato dal database e restituisce "42".
    'PDO step 3: returns the generated ID as a string' => function (): void {
        $pdo = new RecordingPdo();
        $request = new CreateStudentRequest('Mario', 'Rossi', 18, '00123');

        $id = (new StudentRepository($pdo))->create($request);

        assertSame('42', $id);
    },
];

$passed = 0;
$failed = 0;

foreach ($tests as $name => $test) {
    try {
        $test();
        $passed++;
        echo "[PASS] {$name}\n";
    } catch (Throwable $exception) {
        $failed++;
        echo "[FAIL] {$name}\n";
        echo '     ' . str_replace("\n", "\n     ", $exception->getMessage()) . "\n";
    }
}

echo "\nResult: {$passed} passed, {$failed} failed.\n";
exit($failed === 0 ? 0 : 1);
