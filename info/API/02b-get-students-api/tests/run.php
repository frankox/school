<?php

declare(strict_types=1);

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function request(string $path, string $method = 'GET'): array
{
    $context = stream_context_create(['http' => [
        'method' => $method,
        'ignore_errors' => true,
        'timeout' => 2,
    ]]);
    $body = file_get_contents('http://127.0.0.1:' . $GLOBALS['port'] . $path, false, $context);
    check($body !== false, 'La richiesta HTTP non ha ricevuto una risposta.');
    $headers = $http_response_header ?? [];
    preg_match('/\s(\d{3})\s/', $headers[0] ?? '', $matches);
    $data = json_decode($body, true);
    check(json_last_error() === JSON_ERROR_NONE && is_array($data), 'La risposta deve essere JSON valido.');
    return [(int) ($matches[1] ?? 0), $data, $headers];
}

$root = dirname(__DIR__);
$socket = stream_socket_server('tcp://127.0.0.1:0', $errorNumber, $errorMessage);
if ($socket === false) {
    throw new RuntimeException('Impossibile aprire una porta locale per i test HTTP. ' . $errorMessage);
}
$address = stream_socket_get_name($socket, false);
fclose($socket);
$GLOBALS['port'] = (int) substr($address, strrpos($address, ':') + 1);

$env = array_merge($_ENV, [
    'DB_FACTORY_FILE' => __DIR__ . '/fake-database.php',
]);
$log = tempnam(sys_get_temp_dir(), 'students-api-');
$process = proc_open(
    [PHP_BINARY, '-S', '127.0.0.1:' . $GLOBALS['port'], '-t', $root . '/public'],
    [0 => ['pipe', 'r'], 1 => ['file', $log, 'a'], 2 => ['file', $log, 'a']],
    $pipes,
    $root,
    $env
);
check(is_resource($process), 'Impossibile avviare il server PHP.');
fclose($pipes[0]);

try {
    $ready = false;
    for ($attempt = 0; $attempt < 30; $attempt++) {
        usleep(100000);
        $health = @file_get_contents('http://127.0.0.1:' . $GLOBALS['port'] . '/health.php');
        if ($health === '{"status":"ok"}') {
            $ready = true;
            break;
        }
    }
    check($ready, 'Il server PHP non si è avviato.');

    $tests = [
        'GET elenca studenti senza student_code' => static function (): void {
            [$status, $body] = request('/api/students.php');
            check($status === 200, 'Atteso HTTP 200; ricevuto ' . $status);
            check(isset($body['students']) && count($body['students']) === 6, 'Attesi sei studenti.');
            check($body['students'][0] === [
                'id' => 1, 'first_name' => 'Giulia', 'last_name' => 'Bianchi', 'age' => 16,
            ], 'La risposta deve contenere solo id, first_name, last_name e age.');
            check(!str_contains(json_encode($body), 'student_code'), 'student_code è un dato riservato.');
        },
        'GET con min_age filtra tramite WHERE' => static function (): void {
            [$status, $body] = request('/api/students.php?min_age=18');
            check($status === 200, 'Atteso HTTP 200; ricevuto ' . $status);
            check(array_column($body['students'] ?? [], 'id') === [3, 4, 5, 6], 'Il filtro deve includere solo età >= 18.');
        },
        'GET può restituire una lista vuota' => static function (): void {
            [$status, $body] = request('/api/students.php?min_age=100');
            check($status === 200 && $body === ['students' => []], 'Attesa una lista vuota con HTTP 200.');
        },
        'min_age non valido restituisce 422' => static function (): void {
            [$status, $body] = request('/api/students.php?min_age=abc');
            check($status === 422 && isset($body['error']), 'Atteso HTTP 422 e un messaggio di errore.');
        },
        'min_age fuori intervallo restituisce 422' => static function (): void {
            [$status, $body] = request('/api/students.php?min_age=13');
            check($status === 422 && isset($body['error']), 'Atteso HTTP 422 per età minore di 14.');
        },
        'POST restituisce 405 e Allow GET' => static function (): void {
            [$status, $body, $headers] = request('/api/students.php', 'POST');
            check($status === 405 && isset($body['error']), 'Atteso HTTP 405.');
            check(in_array('Allow: GET', $headers, true), 'Manca l’header Allow: GET.');
        },
    ];

    $passed = 0;
    foreach ($tests as $name => $test) {
        try {
            $test();
            $passed++;
            echo "[PASS] {$name}\n";
        } catch (Throwable $exception) {
            echo "[FAIL] {$name}: {$exception->getMessage()}\n";
        }
    }
    echo "\nRisultato: {$passed}/" . count($tests) . " test superati.\n";
    exit($passed === count($tests) ? 0 : 1);
} finally {
    proc_terminate($process);
    proc_close($process);
    unlink($log);
}
