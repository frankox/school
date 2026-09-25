<?php

declare(strict_types=1);

use App\CreateOrderRequest;
use App\OrderService;

require_once dirname(__DIR__, 2) . '/src/OrderItemData.php';
require_once dirname(__DIR__, 2) . '/src/CreateOrderRequest.php';
require_once dirname(__DIR__, 2) . '/src/OrderService.php';

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
    $request = CreateOrderRequest::fromJson($json);
    $createPdo = require dirname(__DIR__, 2) . '/config/database.php';
    $id = (new OrderService($createPdo()))->create($request);
    respond(201, ['id' => $id]);
} catch (InvalidArgumentException $exception) {
    respond(422, ['error' => $exception->getMessage()]);
} catch (DomainException $exception) {
    respond(409, ['error' => $exception->getMessage()]);
} catch (LogicException $exception) {
    respond(501, ['error' => $exception->getMessage()]);
} catch (Throwable) {
    respond(500, ['error' => 'Internal server error.']);
}
