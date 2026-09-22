<?php

declare(strict_types=1);

use App\CsvToSqlConverter;

require_once dirname(__DIR__) . '/src/CsvToSqlConverter.php';

final class TestFailure extends RuntimeException
{
}

function assertSame(string $expected, string $actual): void
{
    if ($expected !== $actual) {
        throw new TestFailure(
            "Expected:\n{$expected}\n\nActual:\n{$actual}"
        );
    }
}

/**
 * @param class-string<Throwable> $expectedClass
 */
function assertThrows(string $expectedClass, Closure $operation): void
{
    try {
        $operation();
    } catch (Throwable $exception) {
        if ($exception instanceof $expectedClass) {
            return;
        }

        $actualClass = $exception::class;
        throw new TestFailure(
            "Expected exception {$expectedClass}, got {$actualClass}."
        );
    }

    throw new TestFailure("Expected exception {$expectedClass}, but none was thrown.");
}

$tests = [
    'converts one row' => function (): void {
        $csv = "first_name,last_name,age\nMario,Rossi,18\n";
        $expected = "INSERT INTO `school`.`students` (`first_name`, `last_name`, `age`) VALUES\n"
            . "('Mario', 'Rossi', 18);";

        $actual = (new CsvToSqlConverter())->convert($csv, 'school', 'students');

        assertSame($expected, $actual);
    },

    'generates one insert with multiple rows' => function (): void {
        $csv = "first_name,last_name,age\nMario,Rossi,18\nAnna,Verdi,19\n";
        $expected = "INSERT INTO `school`.`students` (`first_name`, `last_name`, `age`) VALUES\n"
            . "('Mario', 'Rossi', 18),\n"
            . "('Anna', 'Verdi', 19);";

        $actual = (new CsvToSqlConverter())->convert($csv, 'school', 'students');

        assertSame($expected, $actual);
    },

    'recognizes null integers decimals and leading zeros' => function (): void {
        $csv = "code,quantity,price,notes\n00123,-2,1.50,\n";
        $expected = "INSERT INTO `shop`.`products` (`code`, `quantity`, `price`, `notes`) VALUES\n"
            . "('00123', -2, 1.50, NULL);";

        $actual = (new CsvToSqlConverter())->convert($csv, 'shop', 'products');

        assertSame($expected, $actual);
    },

    'handles CSV commas and SQL apostrophes' => function (): void {
        $csv = "last_name,description\nD'Amico,\"blue, ballpoint pen\"\n";
        $expected = "INSERT INTO `school`.`students` (`last_name`, `description`) VALUES\n"
            . "('D''Amico', 'blue, ballpoint pen');";

        $actual = (new CsvToSqlConverter())->convert($csv, 'school', 'students');

        assertSame($expected, $actual);
    },

    'ignores completely empty rows' => function (): void {
        $csv = "id,first_name\n1,Mario\n\n2,Anna\n";
        $expected = "INSERT INTO `school`.`students` (`id`, `first_name`) VALUES\n"
            . "(1, 'Mario'),\n"
            . "(2, 'Anna');";

        $actual = (new CsvToSqlConverter())->convert($csv, 'school', 'students');

        assertSame($expected, $actual);
    },

    'rejects an empty CSV or a CSV without data rows' => function (): void {
        $converter = new CsvToSqlConverter();

        assertThrows(
            InvalidArgumentException::class,
            fn () => $converter->convert('', 'school', 'students')
        );
        assertThrows(
            InvalidArgumentException::class,
            fn () => $converter->convert("first_name,last_name\n", 'school', 'students')
        );
    },

    'rejects empty or duplicate columns' => function (): void {
        $converter = new CsvToSqlConverter();

        assertThrows(
            InvalidArgumentException::class,
            fn () => $converter->convert("first_name,,age\nMario,Rossi,18\n", 'school', 'students')
        );
        assertThrows(
            InvalidArgumentException::class,
            fn () => $converter->convert("first_name,first_name\nMario,Rossi\n", 'school', 'students')
        );
    },

    'rejects rows with the wrong number of values' => function (): void {
        assertThrows(
            InvalidArgumentException::class,
            fn () => (new CsvToSqlConverter())->convert(
                "first_name,last_name,age\nMario,Rossi\n",
                'school',
                'students'
            )
        );
    },

    'rejects invalid SQL identifiers' => function (): void {
        $converter = new CsvToSqlConverter();

        assertThrows(
            InvalidArgumentException::class,
            fn () => $converter->convert("id,first_name\n1,Mario\n", 'school-test', 'students')
        );
        assertThrows(
            InvalidArgumentException::class,
            fn () => $converter->convert("id,first_name\n1,Mario\n", 'school', 'students;DROP')
        );
        assertThrows(
            InvalidArgumentException::class,
            fn () => $converter->convert("id,full name\n1,Mario\n", 'school', 'students')
        );
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
        echo "     " . str_replace("\n", "\n     ", $exception->getMessage()) . "\n";
    }
}

echo "\nResult: {$passed} passed, {$failed} failed.\n";

exit($failed === 0 ? 0 : 1);
