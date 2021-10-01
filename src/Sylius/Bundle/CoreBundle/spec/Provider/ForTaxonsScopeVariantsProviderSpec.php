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

namespace spec\Sylius\Bundle\CoreBundle\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Provider\VariantsProviderInterface;
use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

final class ForTaxonsScopeVariantsProviderSpec extends ObjectBehavior
{
    function let(
        TaxonRepositoryInterface $taxonRepository,
        ProductVariantRepositoryInterface $productVariantRepository
    ): void {
        $this->beConstructedWith($taxonRepository, $productVariantRepository);
    }

    function it_implements_variants_provider_interface(): void
    {
        $this->shouldImplement(VariantsProviderInterface::class);
    }

    function it_supports_only_for_taxons_catalog_promotion_scope(
        CatalogPromotionScopeInterface $forTaxonsScope,
        CatalogPromotionScopeInterface $forVariantsScope
    ): void {
        $forTaxonsScope->getType()->willReturn(CatalogPromotionScopeInterface::TYPE_FOR_TAXONS);
        $forVariantsScope->getType()->willReturn(CatalogPromotionScopeInterface::TYPE_FOR_VARIANTS);

        $this->supports($forTaxonsScope)->shouldReturn(true);
        $this->supports($forVariantsScope)->shouldReturn(false);
    }

    function it_throws_an_exception_if_there_is_no_taxons_configured_in_the_scope_configuration(
        CatalogPromotionScopeInterface $catalogPromotionScope
    ): void {
        $catalogPromotionScope->getConfiguration()->willReturn([]);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('provideEligibleVariants', [$catalogPromotionScope])
        ;
    }

    function it_provides_variants_for_given_taxon_codes_if_they_exist(
        TaxonRepositoryInterface $taxonRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        CatalogPromotionScopeInterface $catalogPromotionScope,
        TaxonInterface $mugs,
        TaxonInterface $tShirts,
        ProductVariantInterface $firstMug,
        ProductVariantInterface $secondMug,
        ProductVariantInterface $tShirt
    ): void {
        $catalogPromotionScope->getConfiguration()->willReturn(['taxons' => ['MUGS', 'DISHES', 'T-SHIRTS']]);

        $taxonRepository->findOneBy(['code' => 'MUGS'])->willReturn($mugs);
        $taxonRepository->findOneBy(['code' => 'DISHES'])->willReturn(null);
        $taxonRepository->findOneBy(['code' => 'T-SHIRTS'])->willReturn($tShirts);

        $productVariantRepository->findByTaxon($mugs)->willReturn([$firstMug, $secondMug]);
        $productVariantRepository->findByTaxon($tShirts)->willReturn([$tShirt]);

        $this
            ->provideEligibleVariants($catalogPromotionScope)
            ->shouldReturn([$firstMug, $secondMug, $tShirt])
        ;
    }
}
