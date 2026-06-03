<?php

namespace Models;

class Category
{
    public function __construct(
        public string $name,
        public ?int $id = null
    ) {}
}
