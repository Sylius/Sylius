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
use Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionProviderInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;

final class CatalogPromotionEligibilityCheckerSpec extends ObjectBehavior
{
    function let(EligibleCatalogPromotionProviderInterface $eligibleCatalogPromotionProvider): void
    {
        $this->beConstructedWith($eligibleCatalogPromotionProvider);
    }

    function it_implements_catalog_promotion_eligibility_checker_interface(): void
    {
        $this->shouldImplement(CatalogPromotionEligibilityCheckerInterface::class);
    }

    public function it_returns_true_if_catalog_promotion_eligible(
        CatalogPromotionInterface $promotion,
        EligibleCatalogPromotionProviderInterface $eligibleCatalogPromotionProvider
    ): void {
        $eligibleCatalogPromotionProvider->provide($promotion)->willReturn($promotion);

        $this->isCatalogPromotionEligible($promotion);
    }

    public function it_returns_false_if_catalog_promotion_not_eligible(
        CatalogPromotionInterface $promotion,
        EligibleCatalogPromotionProviderInterface $eligibleCatalogPromotionProvider
    ): void {
        $eligibleCatalogPromotionProvider->provide($promotion)->willReturn(null);

        $this->isCatalogPromotionEligible($promotion);
    }
}
