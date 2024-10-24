<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\CatalogPromotionEligibilityCheckerInterface;
use Sylius\Bundle\PromotionBundle\Criteria\CriteriaInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;

final class CatalogPromotionEligibilityCheckerSpec extends ObjectBehavior
{
    function let(CriteriaInterface $firstCriterion, CriteriaInterface $secondCriterion): void
    {
        $this->beConstructedWith([$firstCriterion, $secondCriterion]);
    }

    function it_implements_catalog_promotion_eligibility_checker_interface(): void
    {
        $this->shouldImplement(CatalogPromotionEligibilityCheckerInterface::class);
    }

    public function it_returns_true_if_catalog_promotion_eligible(
        CriteriaInterface $firstCriterion,
        CriteriaInterface $secondCriterion,
        CatalogPromotionInterface $promotion,
    ): void {
        $firstCriterion->verify($promotion)->willReturn(true);
        $secondCriterion->verify($promotion)->willReturn(true);

        $this->isCatalogPromotionEligible($promotion)->shouldReturn(true);
    }

    public function it_returns_false_if_catalog_promotion_not_eligible(
        CriteriaInterface $firstCriterion,
        CriteriaInterface $secondCriterion,
        CatalogPromotionInterface $promotion,
    ): void {
        $firstCriterion->verify($promotion)->willReturn(false);
        $secondCriterion->verify($promotion)->shouldNotBeCalled();

        $this->isCatalogPromotionEligible($promotion)->shouldReturn(false);
    }
}
