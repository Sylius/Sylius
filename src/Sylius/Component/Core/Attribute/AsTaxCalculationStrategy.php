<?php

namespace Sylius\Component\Core\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsTaxCalculationStrategy
{
    public function __construct(
        public string $type,
        public string $label,
        public int $priority = 0
    ) {
    }
}
