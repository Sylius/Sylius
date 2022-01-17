<?php

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\DiscountApplicationCriteria;

use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

final class ExclusiveCriteria implements DiscountApplicationCriteriaInterface
{
    public function isApplicable(CatalogPromotionInterface $catalogPromotion, CatalogPromotionActionInterface $action, ChannelPricingInterface $channelPricing): bool
    {
        return !$channelPricing->hasExclusiveCatalogPromotionApplied();
    }
}
