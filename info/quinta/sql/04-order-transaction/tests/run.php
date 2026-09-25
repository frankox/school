<?php

declare(strict_types=1);

use App\CreateOrderRequest;
use App\OrderService;

require_once dirname(__DIR__) . '/src/OrderItemData.php';
require_once dirname(__DIR__) . '/src/CreateOrderRequest.php';
require_once dirname(__DIR__) . '/src/OrderService.php';

final class TestFailure extends RuntimeException
{
}

function assertSame(mixed $expected, mixed $actual): void
{
    if ($expected !== $actual) {
        throw new TestFailure('Expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
    }
}

function assertThrows(string $class, Closure $operation): void
{
    try {
        $operation();
    } catch (Throwable $exception) {
        if ($exception instanceof $class) {
            return;
        }

        $actualClass = $exception::class;
        throw new TestFailure("Expected {$class}, got {$actualClass}.");
    }

    throw new TestFailure("Expected {$class}, but none was thrown.");
}

final class OrderStatement extends PDOStatement
{
    public array $executions = [];
    public int $affectedRows = 1;

    public function __construct(public readonly string $kind)
    {
    }

    public function execute(?array $params = null): bool
    {
        $this->executions[] = $params;
        return true;
    }

    public function rowCount(): int
    {
        return $this->affectedRows;
    }
}

final class OrderPdo extends PDO
{
    public array $events = [];
    public array $preparedSql = [];
    public OrderStatement $orderStatement;
    public OrderStatement $stockStatement;
    public OrderStatement $itemStatement;

    public function __construct()
    {
        $this->orderStatement = new OrderStatement('order');
        $this->stockStatement = new OrderStatement('stock');
        $this->itemStatement = new OrderStatement('item');
    }

    public function beginTransaction(): bool
    {
        $this->events[] = 'begin';
        return true;
    }

    public function prepare(string $query, array $options = []): PDOStatement|false
    {
        $this->preparedSql[] = $query;

        if (str_starts_with($query, 'INSERT INTO orders ')) {
            return $this->orderStatement;
        }
        if (str_starts_with($query, 'UPDATE products ')) {
            return $this->stockStatement;
        }
        if (str_starts_with($query, 'INSERT INTO order_items ')) {
            return $this->itemStatement;
        }

        throw new RuntimeException("Unexpected SQL: {$query}");
    }

    public function lastInsertId(?string $name = null): string|false
    {
        return '99';
    }

    public function commit(): bool
    {
        $this->events[] = 'commit';
        return true;
    }

    public function rollBack(): bool
    {
        $this->events[] = 'rollback';
        return true;
    }

    public function inTransaction(): bool
    {
        return in_array('begin', $this->events, true)
            && !in_array('commit', $this->events, true)
            && !in_array('rollback', $this->events, true);
    }
}

function validRequest(): CreateOrderRequest
{
    return CreateOrderRequest::fromJson(
        '{"customer_id":7,"items":[{"product_id":10,"quantity":2},{"product_id":20,"quantity":1}]}'
    );
}

$tests = [
    'parses the composite resource' => function (): void {
        $request = validRequest();

        assertSame(7, $request->customerId);
        assertSame(2, count($request->items));
        assertSame(10, $request->items[0]->productId);
        assertSame(2, $request->items[0]->quantity);
    },

    'rejects invalid orders before touching the database' => function (): void {
        assertThrows(
            InvalidArgumentException::class,
            fn () => CreateOrderRequest::fromJson('{"customer_id":7,"items":[]}')
        );
        assertThrows(
            InvalidArgumentException::class,
            fn () => CreateOrderRequest::fromJson(
                '{"customer_id":7,"items":[{"product_id":10,"quantity":0}]}'
            )
        );
    },

    'creates the order and every item in one transaction' => function (): void {
        $pdo = new OrderPdo();

        $id = (new OrderService($pdo))->create(validRequest());

        assertSame(99, $id);
        assertSame(['begin', 'commit'], $pdo->events);
        assertSame([['customer_id' => 7]], $pdo->orderStatement->executions);
        assertSame(2, count($pdo->stockStatement->executions));
        assertSame(2, count($pdo->itemStatement->executions));
        assertSame([
            'order_id' => 99,
            'product_id' => 10,
            'quantity' => 2,
        ], $pdo->itemStatement->executions[0]);
    },

    'prepares each query only once' => function (): void {
        $pdo = new OrderPdo();
        (new OrderService($pdo))->create(validRequest());

        assertSame(3, count($pdo->preparedSql));
    },

    'rolls back when stock is insufficient' => function (): void {
        $pdo = new OrderPdo();
        $pdo->stockStatement->affectedRows = 0;

        assertThrows(
            DomainException::class,
            fn () => (new OrderService($pdo))->create(validRequest())
        );

        assertSame(['begin', 'rollback'], $pdo->events);
        assertSame([], $pdo->itemStatement->executions);
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
        echo '     ' . str_replace("\n", "\n     ", $exception->getMessage()) . "\n";
    }
}

echo "\nResult: {$passed} passed, {$failed} failed.\n";
exit($failed === 0 ? 0 : 1);
