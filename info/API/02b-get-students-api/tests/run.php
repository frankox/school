<?php

declare(strict_types=1);

function assertSameValue(mixed $expected, mixed $actual, string $description): void
{
    if ($expected !== $actual) {
        throw new RuntimeException(
            $description . "\nExpected: " . var_export($expected, true)
            . "\nReceived: " . var_export($actual, true)
        );
    }
}

function assertStatus(int $expected, int $actual, array $body): void
{
    if ($expected !== $actual) {
        throw new RuntimeException(
            "HTTP status\nExpected: {$expected}\nReceived: {$actual}"
            . "\nJSON response: " . json_encode($body, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)
        );
    }
}

function expectedStudentDtos(): array
{
    return [
        ['firstName' => 'Giulia', 'lastName' => 'Bianchi', 'age' => 16, 'studentCode' => 'ST-001'],
        ['firstName' => 'Marco', 'lastName' => 'Rossi', 'age' => 17, 'studentCode' => 'ST-002'],
        ['firstName' => 'Sara', 'lastName' => 'Verdi', 'age' => 18, 'studentCode' => 'ST-003'],
        ['firstName' => 'Luca', 'lastName' => 'Neri', 'age' => 18, 'studentCode' => 'ST-004'],
        ['firstName' => 'Amina', 'lastName' => 'Conti', 'age' => 19, 'studentCode' => 'ST-005'],
        ['firstName' => 'Paolo', 'lastName' => 'Gallo', 'age' => 21, 'studentCode' => 'ST-006'],
    ];
}

function request(string $path, string $method = 'GET'): array
{
    $context = stream_context_create(['http' => [
        'method' => $method,
        'ignore_errors' => true,
        'timeout' => 2,
    ]]);
    $body = file_get_contents('http://127.0.0.1:' . $GLOBALS['port'] . $path, false, $context);
    if ($body === false) {
        throw new RuntimeException('La richiesta HTTP non ha ricevuto una risposta.');
    }
    $headers = $http_response_header ?? [];
    preg_match('/\s(\d{3})\s/', $headers[0] ?? '', $matches);
    $data = json_decode($body, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
        throw new RuntimeException(
            "JSON response\nExpected: a valid JSON array or object"
            . "\nReceived: " . var_export($body, true)
        );
    }
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

$env = array_merge(getenv(), [
    'DB_FACTORY_FILE' => __DIR__ . '/test-database.php',
]);
$log = tempnam(sys_get_temp_dir(), 'students-api-');
error_clear_last();
$process = proc_open(
    [PHP_BINARY, '-S', '127.0.0.1:' . $GLOBALS['port'], '-t', $root . '/public'],
    [0 => ['pipe', 'r'], 1 => ['file', $log, 'a'], 2 => ['file', $log, 'a']],
    $pipes,
    $root,
    $env
);
if (!is_resource($process)) {
    $error = error_get_last()['message'] ?? 'Verificare che PHP possa creare processi.';
    unlink($log);
    throw new RuntimeException('Impossibile avviare il server PHP. ' . $error);
}
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
    if (!$ready) {
        throw new RuntimeException('Il server PHP non si è avviato. ' . file_get_contents($log));
    }

    $tests = [
        'GET returns all StudentDto elements in order' => static function (): void {
            [$status, $body] = request('/api/students.php');
            assertStatus(200, $status, $body);
            assertSameValue(expectedStudentDtos(), $body, 'StudentDto array');
        },
        'GET with min_age returns matching StudentDto elements' => static function (): void {
            [$status, $body] = request('/api/students.php?min_age=18');
            assertStatus(200, $status, $body);
            assertSameValue(array_slice(expectedStudentDtos(), 2), $body, 'Filtered StudentDto array');
        },
        'GET returns an empty array when no students match' => static function (): void {
            [$status, $body] = request('/api/students.php?min_age=100');
            assertStatus(200, $status, $body);
            assertSameValue([], $body, 'Empty StudentDto array');
        },
        'Invalid min_age returns 422' => static function (): void {
            [$status, $body] = request('/api/students.php?min_age=abc');
            assertStatus(422, $status, $body);
            assertSameValue(true, isset($body['error']), 'Error message exists');
        },
        'Out-of-range min_age returns 422' => static function (): void {
            [$status, $body] = request('/api/students.php?min_age=13');
            assertStatus(422, $status, $body);
            assertSameValue(true, isset($body['error']), 'Error message exists');
        },
        'POST returns 405 and Allow GET' => static function (): void {
            [$status, $body, $headers] = request('/api/students.php', 'POST');
            assertStatus(405, $status, $body);
            assertSameValue(true, isset($body['error']), 'Error message exists');
            assertSameValue(true, in_array('Allow: GET', $headers, true), 'Allow: GET header');
        },
    ];

    $passed = 0;
    foreach ($tests as $name => $test) {
        try {
            $test();
            $passed++;
            echo "[PASS] {$name}\n";
        } catch (Throwable $exception) {
            echo "[FAIL] {$name}\n";
            echo '       ' . str_replace("\n", "\n       ", $exception->getMessage()) . "\n";
        }
    }
    echo "\nRisultato: {$passed}/" . count($tests) . " test superati.\n";
    exit($passed === count($tests) ? 0 : 1);
} finally {
    proc_terminate($process);
    proc_close($process);
    unlink($log);
}
