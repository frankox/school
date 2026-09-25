<?php

declare(strict_types=1);

use App\StudentBatchImporter;

require_once dirname(__DIR__) . '/src/StudentData.php';
require_once dirname(__DIR__) . '/src/StudentBatchImporter.php';

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

final class BatchStatement extends PDOStatement
{
    public array $executions = [];
    public ?int $failAt = null;

    public function __construct()
    {
    }

    public function execute(?array $params = null): bool
    {
        $this->executions[] = $params;

        if ($this->failAt === count($this->executions)) {
            throw new PDOException('Simulated insert failure.');
        }

        return true;
    }
}

final class TransactionPdo extends PDO
{
    public array $events = [];
    public int $prepareCount = 0;
    public BatchStatement $statement;

    public function __construct()
    {
        $this->statement = new BatchStatement();
    }

    public function beginTransaction(): bool
    {
        $this->events[] = 'begin';
        return true;
    }

    public function prepare(string $query, array $options = []): PDOStatement|false
    {
        $this->events[] = 'prepare';
        $this->prepareCount++;
        return $this->statement;
    }

    public function commit(): bool
    {
        $this->events[] = 'commit';
        return true;
    }

    public function rollBack(): bool
    {
        $this->events[] = 'rollback';
        return true;
    }

    public function inTransaction(): bool
    {
        return in_array('begin', $this->events, true)
            && !in_array('commit', $this->events, true)
            && !in_array('rollback', $this->events, true);
    }
}

$validJson = json_encode([
    ['first_name' => 'Mario', 'last_name' => 'Rossi', 'age' => 18, 'student_code' => '00123'],
    ['first_name' => 'Anna', 'last_name' => 'Verdi', 'age' => 19, 'student_code' => '00124'],
], JSON_THROW_ON_ERROR);

$tests = [
    'inserts every student using one transaction and one statement' => function () use ($validJson): void {
        $pdo = new TransactionPdo();

        $inserted = (new StudentBatchImporter($pdo))->import($validJson);

        assertSame(2, $inserted);
        assertSame(1, $pdo->prepareCount);
        assertSame(['begin', 'prepare', 'commit'], $pdo->events);
        assertSame(2, count($pdo->statement->executions));
    },

    'preserves typed parameters and leading zeros' => function () use ($validJson): void {
        $pdo = new TransactionPdo();
        (new StudentBatchImporter($pdo))->import($validJson);

        assertSame([
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'age' => 18,
            'student_code' => '00123',
        ], $pdo->statement->executions[0]);
    },

    'rolls back if one insert fails' => function () use ($validJson): void {
        $pdo = new TransactionPdo();
        $pdo->statement->failAt = 2;

        assertThrows(
            PDOException::class,
            fn () => (new StudentBatchImporter($pdo))->import($validJson)
        );

        assertSame(['begin', 'prepare', 'rollback'], $pdo->events);
    },

    'rejects malformed empty or non-list JSON before a transaction' => function (): void {
        foreach (['{', '[]', '{"first_name":"Mario"}'] as $json) {
            $pdo = new TransactionPdo();
            assertThrows(
                InvalidArgumentException::class,
                fn () => (new StudentBatchImporter($pdo))->import($json)
            );
            assertSame([], $pdo->events);
        }
    },

    'validates every student before a transaction' => function () use ($validJson): void {
        $data = json_decode($validJson, true, 512, JSON_THROW_ON_ERROR);
        $data[1]['age'] = '19';
        $pdo = new TransactionPdo();

        assertThrows(
            InvalidArgumentException::class,
            fn () => (new StudentBatchImporter($pdo))->import(json_encode($data, JSON_THROW_ON_ERROR))
        );
        assertSame([], $pdo->events);
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
