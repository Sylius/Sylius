<?php

namespace Sylius\Component\Promotion\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsCatalogPromotionVariantChecker
{
    public function __construct(
        public string $type,
    ) {
    }
}
