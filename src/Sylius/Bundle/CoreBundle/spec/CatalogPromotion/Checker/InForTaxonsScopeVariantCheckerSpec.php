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
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\VariantInScopeCheckerInterface;
use Sylius\Bundle\TaxonomyBundle\Repository\TaxonTreeRepositoryInterface;
use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

final class InForTaxonsScopeVariantCheckerSpec extends ObjectBehavior
{
    function let(TaxonRepositoryInterface $taxonRepository, TaxonTreeRepositoryInterface $taxonTreeRepository): void
    {
        $this->beConstructedWith($taxonRepository, $taxonTreeRepository);
    }

    function it_implements_catalog_promotion_price_calculator_interface(): void
    {
        $this->shouldImplement(VariantInScopeCheckerInterface::class);
    }

    public function it_returns_true_if_variant_taxon_is_in_scope_configuration(
        TaxonRepositoryInterface $taxonRepository,
        TaxonTreeRepositoryInterface $taxonTreeRepository,
        CatalogPromotionScopeInterface $scope,
        ProductVariantInterface $variant,
        ProductInterface $product,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon,
        TaxonInterface $thirdTaxon,
        TaxonInterface $fourthTaxon,
    ): void {
        $taxonRepository->findOneBy(['code' => 'FIRST_TAXON'])->willReturn($firstTaxon);
        $taxonRepository->findOneBy(['code' => 'SECOND_TAXON'])->willReturn($secondTaxon);
        $taxonRepository->findOneBy(['code' => 'THIRD_TAXON'])->willReturn($thirdTaxon);
        $taxonRepository->findOneBy(['code' => 'FOURTH_TAXON'])->willReturn($fourthTaxon);

        $taxonTreeRepository->children($firstTaxon)->willReturn([]);
        $taxonTreeRepository->children($secondTaxon)->willReturn([]);
        $taxonTreeRepository->children($thirdTaxon)->willReturn([]);
        $taxonTreeRepository->children($fourthTaxon)->willReturn([]);

        $scope->getConfiguration()->willReturn(['taxons' => ['FIRST_TAXON', 'SECOND_TAXON']]);

        $variant->getProduct()->willReturn($product);
        $product->getTaxons()->willReturn(new ArrayCollection([$firstTaxon->getWrappedObject(), $thirdTaxon->getWrappedObject()]));

        $firstTaxon->getCode()->willReturn('FIRST_TAXON');
        $secondTaxon->getCode()->willReturn('SECOND_TAXON');
        $thirdTaxon->getCode()->willReturn('THIRD_TAXON');
        $fourthTaxon->getCode()->willReturn('FOURTH_TAXON');

        $this->inScope($scope, $variant)->shouldReturn(true);
    }

    public function it_returns_true_if_variant_taxon_is_a_child_of_taxon_in_the_scope_configuration(
        TaxonRepositoryInterface $taxonRepository,
        TaxonTreeRepositoryInterface $taxonTreeRepository,
        CatalogPromotionScopeInterface $scope,
        ProductVariantInterface $variant,
        ProductInterface $product,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon,
    ): void {
        $taxonRepository->findOneBy(['code' => 'FIRST_TAXON'])->willReturn($firstTaxon);

        $taxonTreeRepository->children($firstTaxon)->willReturn([$secondTaxon]);

        $scope->getConfiguration()->willReturn(['taxons' => ['FIRST_TAXON']]);

        $variant->getProduct()->willReturn($product);
        $product->getTaxons()->willReturn(new ArrayCollection([$secondTaxon->getWrappedObject()]));

        $firstTaxon->getCode()->willReturn('FIRST_TAXON');
        $secondTaxon->getCode()->willReturn('SECOND_TAXON');

        $this->inScope($scope, $variant)->shouldReturn(true);
    }

    public function it_returns_false_if_variant_taxon_is_not_in_scope_configuration(
        TaxonRepositoryInterface $taxonRepository,
        TaxonTreeRepositoryInterface $taxonTreeRepository,
        CatalogPromotionScopeInterface $scope,
        ProductVariantInterface $variant,
        ProductInterface $product,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon,
        TaxonInterface $thirdTaxon,
    ): void {
        $taxonRepository->findOneBy(['code' => 'FIRST_TAXON'])->willReturn($firstTaxon);
        $taxonRepository->findOneBy(['code' => 'SECOND_TAXON'])->willReturn($secondTaxon);

        $taxonTreeRepository->children($firstTaxon)->willReturn([]);
        $taxonTreeRepository->children($secondTaxon)->willReturn([]);

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
