<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Checker;

use Sylius\Component\Core\Model\CatalogPromotionInterface;

interface CatalogPromotionEligibilityCheckerInterface
{
    public function isCatalogPromotionEligible(CatalogPromotionInterface $promotion): bool;

    public function isCatalogPromotionEligibleOperatingTime(CatalogPromotionInterface $promotion): bool;
}
