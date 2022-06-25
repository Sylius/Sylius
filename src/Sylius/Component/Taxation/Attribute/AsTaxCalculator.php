<?php

namespace Sylius\Component\Taxation\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsTaxCalculator
{
    public function __construct(
        public string $calculator,
    ) {
    }
}
