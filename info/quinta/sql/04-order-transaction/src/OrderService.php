<?php

declare(strict_types=1);

namespace App;

use LogicException;
use PDO;

final class OrderService
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function create(CreateOrderRequest $request): int
    {
        // TODO: implement the transactional workflow described in the README.
        throw new LogicException('Order creation not implemented yet.');
    }
}
