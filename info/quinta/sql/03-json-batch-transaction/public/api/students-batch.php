<?php

declare(strict_types=1);

use App\StudentBatchImporter;

require_once dirname(__DIR__, 2) . '/src/StudentData.php';
require_once dirname(__DIR__, 2) . '/src/StudentBatchImporter.php';

function respond(int $status, array $body): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    respond(405, ['error' => 'Method not allowed. Use POST.']);
}

$json = file_get_contents('php://input');

if ($json === false) {
    respond(400, ['error' => 'Unable to read the request body.']);
}

try {
    $createPdo = require dirname(__DIR__, 2) . '/config/database.php';
    $inserted = (new StudentBatchImporter($createPdo()))->import($json);
    respond(201, ['inserted' => $inserted]);
} catch (InvalidArgumentException $exception) {
    respond(422, ['error' => $exception->getMessage()]);
} catch (LogicException $exception) {
    respond(501, ['error' => $exception->getMessage()]);
} catch (Throwable) {
    respond(500, ['error' => 'Internal server error.']);
}
