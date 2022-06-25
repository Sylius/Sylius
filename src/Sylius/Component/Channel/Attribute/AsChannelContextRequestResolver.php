<?php

namespace Sylius\Component\Channel\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsChannelContextRequestResolver
{
    public function __construct(
        public int $priority = 0,
    ) {
    }
}
