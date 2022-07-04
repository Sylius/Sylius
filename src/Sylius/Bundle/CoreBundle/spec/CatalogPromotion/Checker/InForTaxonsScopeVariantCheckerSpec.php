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

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\Checker;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\VariantInScopeCheckerInterface;
use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class InForTaxonsScopeVariantCheckerSpec extends ObjectBehavior
{
    function it_implements_catalog_promotion_price_calculator_interface(): void
    {
        $this->shouldImplement(VariantInScopeCheckerInterface::class);
    }

    public function it_returns_true_if_variant_taxon_is_in_scope_configuration(
        CatalogPromotionScopeInterface $scope,
        ProductVariantInterface $variant,
        ProductInterface $product,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon,
        TaxonInterface $thirdTaxon,
    ): void {
        $scope->getConfiguration()->willReturn(['taxons' => ['FIRST_TAXON', 'SECOND_TAXON']]);

        $variant->getProduct()->willReturn($product);
        $product->getTaxons()->willReturn(new ArrayCollection([$firstTaxon->getWrappedObject(), $thirdTaxon->getWrappedObject()]));

        $firstTaxon->getCode()->willReturn('FIRST_TAXON');
        $secondTaxon->getCode()->willReturn('SECOND_TAXON');
        $thirdTaxon->getCode()->willReturn('THIRD_TAXON');

        $this->inScope($scope, $variant)->shouldReturn(true);
    }

    public function it_returns_false_if_variant_taxon_is_not_in_scope_configuration(
        CatalogPromotionScopeInterface $scope,
        ProductVariantInterface $variant,
        ProductInterface $product,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon,
        TaxonInterface $thirdTaxon,
    ): void {
        $scope->getConfiguration()->willReturn(['taxons' => ['FIRST_TAXON', 'SECOND_TAXON']]);

        $variant->getProduct()->willReturn($product);
        $product->getTaxons()->willReturn(new ArrayCollection([$thirdTaxon->getWrappedObject()]));

        $firstTaxon->getCode()->willReturn('FIRST_TAXON');
        $secondTaxon->getCode()->willReturn('SECOND_TAXON');
        $thirdTaxon->getCode()->willReturn('THIRD_TAXON');

        $this->inScope($scope, $variant)->shouldReturn(false);
    }

    public function it_throws_exception_if_scope_does_not_contains_product_configuration(
        CatalogPromotionScopeInterface $scope,
        ProductVariantInterface $variant,
    ): void {
        $scope->getConfiguration()->willReturn(['FOO' => ['BOO']]);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('inScope', [$scope, $variant])
        ;
    }
}
