<?php

namespace Sylius\Component\Locale\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsLocaleContext
{
    public function __construct(
        public int $priority = 0,
    ) {
    }
}
