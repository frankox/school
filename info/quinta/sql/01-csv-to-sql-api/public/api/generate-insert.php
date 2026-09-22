<?php

declare(strict_types=1);

use App\CsvToSqlConverter;

require_once dirname(__DIR__, 2) . '/src/CsvToSqlConverter.php';

function respondWithJson(int $status, string $message): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode(
        ['error' => $message],
        JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
    );

    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    respondWithJson(405, 'Method not allowed. Use POST.');
}

$database = $_GET['database'] ?? '';
$table = $_GET['table'] ?? '';
$csv = file_get_contents('php://input');

if ($csv === false) {
    respondWithJson(400, 'Unable to read the request body.');
}

try {
    $sql = (new CsvToSqlConverter())->convert($csv, $database, $table);

    http_response_code(200);
    header('Content-Type: application/sql; charset=utf-8');
    echo $sql;
} catch (InvalidArgumentException $exception) {
    respondWithJson(400, $exception->getMessage());
} catch (LogicException $exception) {
    respondWithJson(501, $exception->getMessage());
} catch (Throwable) {
    respondWithJson(500, 'Internal server error.');
}
