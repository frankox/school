<?php

declare(strict_types=1);

namespace App;

use LogicException;

final class CsvToSqlConverter
{
    public function convert(string $csv, string $database, string $table): string
    {
        $stream = fopen("php://temp", 'r+');

        if(!$stream) {
            throw new LogicException('Could not open temporary stream.');
        }


        fwrite($stream, $csv);
        rewind($stream);

        // TODO: implement the conversion described in the README
        // and make every test in tests/run.php pass. 

        $columns = fgetcsv($stream, null, ',', '"', ''); 

        $rows = [];
    
        throw new LogicException('Converter not implemented yet.');
    }
}