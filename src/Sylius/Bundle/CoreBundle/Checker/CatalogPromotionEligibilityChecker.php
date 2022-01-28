<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Checker;

use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionStates;

final class CatalogPromotionEligibilityChecker implements CatalogPromotionEligibilityCheckerInterface
{
    public function __construct(private DateTimeProviderInterface $dateTimeProvider)
    {
    }

    public function isCatalogPromotionEligible(CatalogPromotionInterface $promotion): bool
    {
        return $promotion->isEnabled() && $promotion->getState() !== CatalogPromotionStates::STATE_FAILED;
    }

    public function isCatalogPromotionEligibleOperatingTime(CatalogPromotionInterface $promotion): bool
    {
        return $promotion->getStartDate() <= $this->dateTimeProvider->now();
    }
}
