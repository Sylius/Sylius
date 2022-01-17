<?php

namespace Sylius\Bundle\PromotionBundle\DiscountApplicationCriteria;

use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

interface DiscountApplicationCriteriaInterface
{
    public function isApplicable(CatalogPromotionInterface $catalogPromotion, CatalogPromotionActionInterface $action, ChannelPricingInterface $channelPricing): bool;
}
