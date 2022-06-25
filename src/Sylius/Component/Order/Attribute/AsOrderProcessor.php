<?php

namespace Sylius\Component\Order\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsOrderProcessor
{
    public function __construct(
        public int $priority = 0,
    ) {
    }
}
