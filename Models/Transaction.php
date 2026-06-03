<?php

namespace Models;

class Transaction
{
    public function __construct(
        public int $user_id,
        public int $source_id,
        public int $category_id,
        public float $amount,
        public string $type,
        public ?string $comment = null,
        public ?string $date = null,
        public ?int $id = null
    ) {}
}
