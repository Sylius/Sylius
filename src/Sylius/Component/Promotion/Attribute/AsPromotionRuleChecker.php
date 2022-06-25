<?php

namespace Sylius\Component\Promotion\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsPromotionRuleChecker
{
    public function __construct(
        public string $type,
        public string $label,
        public string $formType,
    ) {
    }
}
