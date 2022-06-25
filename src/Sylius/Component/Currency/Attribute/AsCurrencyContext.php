<?php

namespace Sylius\Component\Currency\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsCurrencyContext
{
    public function __construct(
        public int $priority = 0,
    ) {
    }
}
