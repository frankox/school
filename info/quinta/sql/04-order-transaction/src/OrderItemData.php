<?php

declare(strict_types=1);

namespace App;

final class OrderItemData
{
    public function __construct(
        public readonly int $productId,
        public readonly int $quantity,
    ) {
    }
}
