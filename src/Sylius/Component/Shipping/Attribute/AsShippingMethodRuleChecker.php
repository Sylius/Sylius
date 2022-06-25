<?php

namespace Sylius\Component\Shipping\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsShippingMethodRuleChecker
{
    public function __construct(
        public string $type,
        public string $label,
        public string $formType,
    ) {
    }
}
