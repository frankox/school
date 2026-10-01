<?php

declare(strict_types=1);

namespace App;

final class OrderItemData
{
    public function __construct(
        public int $productId,
        public int $quantity,
    ) {
    }
}
