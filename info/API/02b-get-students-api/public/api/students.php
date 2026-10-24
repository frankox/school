<?php

declare(strict_types=1);

use App\StudentRepository;

require_once dirname(__DIR__, 2) . '/src/StudentRepository.php';

function respond(int $status, array $body): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    header('Allow: GET');
    respond(405, ['error' => 'Metodo non consentito. Usa GET.']);
}

try {
    $createPdo = require dirname(__DIR__, 2) . '/config/database.php';
    $pdo = $createPdo();

    // TODO: leggere e validare l'eventuale parametro min_age.
    // TODO: usare StudentRepository per leggere gli studenti.
    // TODO: rispondere con 200 e un array JSON di StudentDto.
    throw new LogicException('Completa la GET degli studenti.');
} catch (InvalidArgumentException $exception) {
    respond(422, ['error' => $exception->getMessage()]);
} catch (LogicException $exception) {
    respond(501, ['error' => $exception->getMessage()]);
} catch (Throwable $exception) {
    error_log($exception->getMessage());
    respond(500, ['error' => $exception->getMessage()]);
}

function getMinAgeFromRequest(): int|null
{
    // TODO: restituire null se min_age manca, altrimenti validare l'intero
    //       tra 14 e 100; per valori non validi lanciare InvalidArgumentException.
    throw new LogicException('Completa la lettura del filtro min_age.');
}
