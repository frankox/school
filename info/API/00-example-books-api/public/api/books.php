<?php

declare(strict_types=1);

function reply(int $status, array $body): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

function requestedId(): int
{
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id === null || $id === false) {
        throw new InvalidArgumentException('id deve essere un intero positivo nella URL.');
    }
    return $id;
}

function readBook(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false) {
        throw new InvalidArgumentException('Impossibile leggere il corpo della richiesta.');
    }

    try {
        $book = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $exception) {
        throw new InvalidArgumentException('JSON non valido.', 0, $exception);
    }

    if (!is_array($book) || array_values($book) === $book) {
        throw new InvalidArgumentException('Invia un oggetto JSON.');
    }

    $keys = array_keys($book);
    sort($keys);
    if ($keys !== ['author', 'publication_year', 'title']) {
        throw new InvalidArgumentException('Servono esattamente title, author e publication_year.');
    }

    if (!is_string($book['title']) || trim($book['title']) === ''
        || !is_string($book['author']) || trim($book['author']) === ''
        || !is_int($book['publication_year']) || $book['publication_year'] < 1 || $book['publication_year'] > 2100) {
        throw new InvalidArgumentException('Titolo/autore non vuoti e anno intero tra 1 e 2100.');
    }

    return $book;
}

$method = $_SERVER['REQUEST_METHOD'] ?? '';
if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'], true)) {
    header('Allow: GET, POST, PUT, DELETE');
    reply(405, ['error' => 'Metodo non consentito.']);
}

try {
    $createPdo = require dirname(__DIR__, 2) . '/config/database.php';
    $pdo = $createPdo();

    if ($method === 'GET' && !isset($_GET['id'])) {
        $books = $pdo->query('SELECT id, title, author, publication_year FROM books ORDER BY id')->fetchAll();
        reply(200, ['books' => $books]);
    }

    if ($method === 'GET') {
        $statement = $pdo->prepare('SELECT id, title, author, publication_year FROM books WHERE id = :id');
        $statement->execute(['id' => requestedId()]);
        $book = $statement->fetch();
        if ($book === false) {
            reply(404, ['error' => 'Libro non trovato.']);
        }
        reply(200, ['book' => $book]);
    }

    if ($method === 'POST') {
        $book = readBook();
        $statement = $pdo->prepare(
            'INSERT INTO books (title, author, publication_year) VALUES (:title, :author, :publication_year)'
        );
        $statement->execute($book);
        reply(201, ['id' => (int) $pdo->lastInsertId()]);
    }

    $id = requestedId();
    if ($method === 'PUT') {
        $book = readBook();
        $book['id'] = $id;
        $statement = $pdo->prepare(
            'UPDATE books SET title = :title, author = :author, publication_year = :publication_year WHERE id = :id'
        );
        $statement->execute($book);
        // rowCount può valere zero anche quando la riga esiste ma i valori non cambiano.
        $check = $pdo->prepare('SELECT id FROM books WHERE id = :id');
        $check->execute(['id' => $id]);
        if ($check->fetch() === false) {
            reply(404, ['error' => 'Libro non trovato.']);
        }
        reply(200, ['id' => $id]);
    }

    $statement = $pdo->prepare('DELETE FROM books WHERE id = :id');
    $statement->execute(['id' => $id]);
    if ($statement->rowCount() === 0) {
        reply(404, ['error' => 'Libro non trovato.']);
    }
    reply(200, ['deleted' => $id]);
} catch (InvalidArgumentException $exception) {
    reply(422, ['error' => $exception->getMessage()]);
} catch (PDOException $exception) {
    error_log($exception->getMessage());
    reply(500, ['error' => 'Errore database: controlla server, schema e credenziali.']);
} catch (Throwable $exception) {
    error_log($exception->getMessage());
    reply(500, ['error' => 'Errore interno.']);
}
