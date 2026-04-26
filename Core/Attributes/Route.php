<?php

namespace Core\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class Route
{
    public function __construct(
        public readonly string $path = '',
        public readonly string $method = 'GET'
    ) {}
}
