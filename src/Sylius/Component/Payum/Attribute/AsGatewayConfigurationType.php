<?php

namespace Sylius\Component\Payum\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsGatewayConfigurationType
{
    public function __construct(
        public string $type,
        public string $label,
        public int $priority = 0
    ) {
    }
}
