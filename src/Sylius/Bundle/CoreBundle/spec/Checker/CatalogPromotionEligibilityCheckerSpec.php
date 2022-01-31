<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Checker\CatalogPromotionEligibilityCheckerInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionStates;

final class CatalogPromotionEligibilityCheckerSpec extends ObjectBehavior
{
    function let(DateTimeProviderInterface $dateTimeProvider): void
    {
        $this->beConstructedWith($dateTimeProvider);
    }

    function it_implements_catalog_promotion_eligibility_checker_interface(): void
    {
        $this->shouldImplement(CatalogPromotionEligibilityCheckerInterface::class);
    }

    public function it_return_true_if_catalog_promotion_eligible(
        CatalogPromotionInterface $promotion,
    ): void {
        $promotion->isEnabled()->willReturn(true);
        $promotion->getState()->willReturn(CatalogPromotionStates::STATE_PROCESSING);

        $this->isCatalogPromotionEligible($promotion);
    }

    public function it_return_false_if_catalog_promotion_not_eligible(
        CatalogPromotionInterface $promotion,
    ): void {
        $promotion->isEnabled()->willReturn(false);
        $promotion->getState()->willReturn(CatalogPromotionStates::STATE_PROCESSING);

        $this->isCatalogPromotionEligible($promotion);
    }

    public function it_return_true_if_catalog_promotion_eligible_operating_time(
        CatalogPromotionInterface $promotion,
        DateTimeProviderInterface $dateTimeProvider
    ): void {
        $startDateTime = new \DateTime('2021-10-10');
        $nowDateTime = new \DateTime('2022-01-30');

        $promotion->getStartDate()->willReturn($startDateTime);
        $dateTimeProvider->now()->willReturn($nowDateTime);

        $this->isCatalogPromotionEligibleOperatingTime($promotion);
    }

    public function it_return_false_if_catalog_promotion_not_eligible_operating_time(
        CatalogPromotionInterface $promotion,
        DateTimeProviderInterface $dateTimeProvider
    ): void {
        $nowDateTime = new \DateTime('2022-01-30');

        $promotion->getStartDate()->willReturn(null);
        $dateTimeProvider->now()->willReturn($nowDateTime);

        $this->isCatalogPromotionEligibleOperatingTime($promotion);
    }
}
