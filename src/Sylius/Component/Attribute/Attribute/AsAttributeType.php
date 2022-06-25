<?php

namespace Sylius\Component\Attribute\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsAttributeType
{
    public function __construct(
        public string $attributeType,
        public string $label,
        public string $formType,
        public ?string $configurationFormType = null
    ) {
    }
}
