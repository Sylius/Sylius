<?php

namespace Sylius\Component\Channel\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsChannelContext
{
    public function __construct(
        public int $priority = 0,
    ) {
    }
}
