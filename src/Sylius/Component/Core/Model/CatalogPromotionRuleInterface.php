<?php

declare(strict_types=1);

namespace Sylius\Component\Core\Model;

use Sylius\Component\Promotion\Model\CatalogPromotionRuleInterface as BaseCatalogPromotionRuleInterface;

interface CatalogPromotionRuleInterface extends BaseCatalogPromotionRuleInterface
{
    public const TYPE_FOR_VARIANTS = 'for_variants';
}
