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

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForTaxonsScopeVariantChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForVariantsScopeVariantChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\ProductVariantForCatalogPromotionEligibilityInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\VariantInScopeCheckerInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class ProductVariantForCatalogPromotionEligibilitySpec extends ObjectBehavior
{
    function let(ServiceLocator $locator): void
    {
        $this->beConstructedWith($locator);
    }

    function it_implements_catalog_promotion_price_calculator_interface(): void
    {
        $this->shouldImplement(ProductVariantForCatalogPromotionEligibilityInterface::class);
    }

    public function it_return_true_if_variant_fits_for_any_catalog_promotion_scope_configuration(
        CatalogPromotionInterface $promotion,
        ProductVariantInterface $variant,
        ServiceLocator $locator,
        CatalogPromotionScopeInterface $forVariantsScope,
        VariantInScopeCheckerInterface $forVariantsChecker,
    ): void {
        $promotion->getScopes()->willReturn(new ArrayCollection([$forVariantsScope->getWrappedObject()]));

        $forVariantsScope->getType()->willReturn(InForVariantsScopeVariantChecker::TYPE);

        $locator->get(InForVariantsScopeVariantChecker::TYPE)->willReturn($forVariantsChecker);

        $forVariantsChecker->inScope($forVariantsScope, $variant)->willReturn(true);

        $this->isApplicableOnVariant($promotion, $variant)->shouldReturn(true);
    }

    public function it_return_false_if_variant_does_not_fit_for_any_catalog_promotion_scope_configuration(
        CatalogPromotionInterface $promotion,
        ProductVariantInterface $variant,
        ServiceLocator $locator,
        CatalogPromotionScopeInterface $forTaxonsScope,
        VariantInScopeCheckerInterface $forTaxonsChecker,
    ): void {
        $promotion->getScopes()->willReturn(new ArrayCollection([$forTaxonsScope->getWrappedObject()]));

        $forTaxonsScope->getType()->willReturn(InForTaxonsScopeVariantChecker::TYPE);
        $locator->get(InForTaxonsScopeVariantChecker::TYPE)->willReturn($forTaxonsChecker);

        $forTaxonsChecker->inScope($forTaxonsScope, $variant)->willReturn(false);

        $this->isApplicableOnVariant($promotion, $variant)->shouldReturn(false);
    }
}
