<?php

namespace Sylius\Component\Payment\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsPaymentMethodResolver
{
    public function __construct(
        public string $type,
        public string $label,
        public int $priority = 0
    ){

    }
}
