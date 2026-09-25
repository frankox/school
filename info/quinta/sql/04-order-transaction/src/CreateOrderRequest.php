<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;
use JsonException;

final class CreateOrderRequest
{
    /** @param list<OrderItemData> $items */
    public function __construct(
        public readonly int $customerId,
        public readonly array $items,
    ) {
    }

    public static function fromJson(string $json): self
    {
        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidArgumentException('Malformed JSON.', previous: $exception);
        }

        if (!is_array($data) || array_is_list($data)) {
            throw new InvalidArgumentException('The request body must be a JSON object.');
        }

        $keys = array_keys($data);
        sort($keys);
        if ($keys !== ['customer_id', 'items']) {
            throw new InvalidArgumentException('The request must contain exactly customer_id and items.');
        }

        if (!is_int($data['customer_id']) || $data['customer_id'] <= 0) {
            throw new InvalidArgumentException('customer_id must be a positive integer.');
        }

        if (!is_array($data['items']) || !array_is_list($data['items']) || $data['items'] === []) {
            throw new InvalidArgumentException('items must be a non-empty array.');
        }

        $items = [];
        foreach ($data['items'] as $item) {
            if (!is_array($item) || array_is_list($item)) {
                throw new InvalidArgumentException('Each item must be a JSON object.');
            }

            $itemKeys = array_keys($item);
            sort($itemKeys);
            if ($itemKeys !== ['product_id', 'quantity']) {
                throw new InvalidArgumentException('Each item must contain product_id and quantity.');
            }

            if (!is_int($item['product_id']) || $item['product_id'] <= 0) {
                throw new InvalidArgumentException('product_id must be a positive integer.');
            }

            if (!is_int($item['quantity']) || $item['quantity'] <= 0) {
                throw new InvalidArgumentException('quantity must be a positive integer.');
            }

            $items[] = new OrderItemData($item['product_id'], $item['quantity']);
        }

        return new self($data['customer_id'], $items);
    }
}
