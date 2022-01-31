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

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Checker\ProductVariantForCatalogPromotionEligibilityInterface;
use Sylius\Bundle\CoreBundle\Checker\VariantInScopeCheckerInterface;
use Sylius\Bundle\CoreBundle\Provider\ForVariantInCatalogPromotionScopeCheckerProviderInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductVariantForCatalogPromotionEligibilitySpec extends ObjectBehavior
{
    function let(ForVariantInCatalogPromotionScopeCheckerProviderInterface $checkerProvider): void
    {
        $this->beConstructedWith($checkerProvider);
    }

    function it_implements_catalog_promotion_price_calculator_interface(): void
    {
        $this->shouldImplement(ProductVariantForCatalogPromotionEligibilityInterface::class);
    }

    public function it_return_true_if_variant_fits_for_any_catalog_promotion_scope_configuration(
        CatalogPromotionInterface $promotion,
        ProductVariantInterface $variant,
        CatalogPromotionScopeInterface $forTaxonsScope,
        ForVariantInCatalogPromotionScopeCheckerProviderInterface $checkerProvider,
        VariantInScopeCheckerInterface $forTaxonsChecker
    ): void {
        $promotion->getScopes()->willReturn(new ArrayCollection([$forTaxonsScope->getWrappedObject()]));

        $checkerProvider->provide($forTaxonsScope)->willReturn($forTaxonsChecker);

        $forTaxonsChecker->inScope($forTaxonsScope, $variant)->willReturn(true);

        $this->isApplicableOnVariant($promotion, $variant)->shouldReturn(true);
    }

    public function it_return_false_if_variant_does_not_fit_for_any_catalog_promotion_scope_configuration(
        CatalogPromotionInterface $promotion,
        ProductVariantInterface $variant,
        CatalogPromotionScopeInterface $forTaxonsScope,
        ForVariantInCatalogPromotionScopeCheckerProviderInterface $checkerProvider,
        VariantInScopeCheckerInterface $forTaxonsChecker
    ): void {
        $promotion->getScopes()->willReturn(new ArrayCollection([$forTaxonsScope->getWrappedObject()]));

        $checkerProvider->provide($forTaxonsScope)->willReturn($forTaxonsChecker);

        $forTaxonsChecker->inScope($forTaxonsScope, $variant)->willReturn(false);

        $this->isApplicableOnVariant($promotion, $variant)->shouldReturn(false);
    }
}
