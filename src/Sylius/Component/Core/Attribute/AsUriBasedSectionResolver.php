<?php

namespace Sylius\Component\Core\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsUriBasedSectionResolver
{
    public function __construct(
        public int $priority = 0,
    ) {
    }
}
