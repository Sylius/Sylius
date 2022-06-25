<?php

namespace Sylius\Component\Order\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsCartContext
{
    public function __construct(
        public int $priority = 0,
    ) {
    }
}
