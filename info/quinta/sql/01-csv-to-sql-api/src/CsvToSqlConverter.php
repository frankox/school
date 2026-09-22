<?php

declare(strict_types=1);

namespace App;

use LogicException;

final class CsvToSqlConverter
{
    public function convert(string $csv, string $database, string $table): string
    {
        // TODO: implement the conversion described in the README
        // and make every test in tests/run.php pass.
        throw new LogicException('Converter not implemented yet.');
    }
}