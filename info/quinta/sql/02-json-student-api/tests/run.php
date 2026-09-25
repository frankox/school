<?php

declare(strict_types=1);

use App\CreateStudentRequest;
use App\StudentRepository;

require_once dirname(__DIR__) . '/src/CreateStudentRequest.php';
require_once dirname(__DIR__) . '/src/StudentRepository.php';

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

final class RecordingStatement extends PDOStatement
{
    public ?array $parameters = null;

    public function __construct()
    {
    }

    public function execute(?array $params = null): bool
    {
        $this->parameters = $params;
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
    'creates a typed request and preserves leading zeros' => function (): void {
        $request = CreateStudentRequest::fromJson(
            '{"first_name":"Mario","last_name":"Rossi","age":18,"student_code":"00123"}'
        );

        assertSame('Mario', $request->firstName);
        assertSame('Rossi', $request->lastName);
        assertSame(18, $request->age);
        assertSame('00123', $request->studentCode);
    },

    'rejects malformed JSON and a non-object root' => function (): void {
        assertThrows(InvalidArgumentException::class, fn () => CreateStudentRequest::fromJson('{'));
        assertThrows(InvalidArgumentException::class, fn () => CreateStudentRequest::fromJson('[]'));
    },

    'rejects missing and unexpected fields' => function (): void {
        assertThrows(
            InvalidArgumentException::class,
            fn () => CreateStudentRequest::fromJson(
                '{"first_name":"Mario","last_name":"Rossi","age":18}'
            )
        );
        assertThrows(
            InvalidArgumentException::class,
            fn () => CreateStudentRequest::fromJson(
                '{"first_name":"Mario","last_name":"Rossi","age":18,"student_code":"1","admin":true}'
            )
        );
    },

    'validates strings and age' => function (): void {
        assertThrows(
            InvalidArgumentException::class,
            fn () => CreateStudentRequest::fromJson(
                '{"first_name":"","last_name":"Rossi","age":18,"student_code":"1"}'
            )
        );
        assertThrows(
            InvalidArgumentException::class,
            fn () => CreateStudentRequest::fromJson(
                '{"first_name":"Mario","last_name":"Rossi","age":"18","student_code":"1"}'
            )
        );
        assertThrows(
            InvalidArgumentException::class,
            fn () => CreateStudentRequest::fromJson(
                '{"first_name":"Mario","last_name":"Rossi","age":12,"student_code":"1"}'
            )
        );
    },

    'uses a prepared statement and returns the generated id' => function (): void {
        $pdo = new RecordingPdo();
        $request = new CreateStudentRequest('Mario', 'Rossi', 18, '00123');

        $id = (new StudentRepository($pdo))->create($request);

        assertSame(
            'INSERT INTO students (first_name, last_name, age, student_code) '
                . 'VALUES (:first_name, :last_name, :age, :student_code)',
            $pdo->sql
        );
        assertSame([
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'age' => 18,
            'student_code' => '00123',
        ], $pdo->statement->parameters);
        assertSame(42, $id);
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
