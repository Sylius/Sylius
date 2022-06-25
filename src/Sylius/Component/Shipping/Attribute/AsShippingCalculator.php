<?php

namespace Sylius\Component\Shipping\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsShippingCalculator
{
    public function __construct(
        public string $calculator,
        public string $label,
        public ?string $formType = null
    ) {
    }
}
