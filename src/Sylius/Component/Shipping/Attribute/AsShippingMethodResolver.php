<?php

namespace Sylius\Component\Shipping\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsShippingMethodResolver
{
    public function __construct(
        public string $type,
        public string $label,
        public int $priority = 0,
    ) {
    }
}
