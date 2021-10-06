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

namespace spec\Sylius\Bundle\CoreBundle\Validator\CatalogPromotionScope;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Validator\CatalogPromotionScope\ScopeValidatorInterface;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CatalogPromotionScope;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

final class ForTaxonsScopeValidatorSpec extends ObjectBehavior
{
    function let(TaxonRepositoryInterface $taxonRepository): void
    {
        $this->beConstructedWith($taxonRepository);
    }

    function it_is_a_scope_validator(): void
    {
        $this->shouldHaveType(ScopeValidatorInterface::class);
    }

    function it_prepares_array_with_violation_if_catalog_promotion_scope_does_not_have_taxons_key_configured(): void
    {
        $this
            ->validate([], new CatalogPromotionScope())
            ->shouldReturn(['configuration.taxons' => 'sylius.catalog_promotion_scope.for_taxons.not_empty'])
        ;
    }

    function it_prepares_array_with_violation_if_catalog_promotion_scope_has_not_existing_taxons_configured(
        TaxonRepositoryInterface $taxonRepository
    ): void {
        $taxonRepository->findOneBy(['code' => 'not_existing_taxon'])->willReturn(null);

        $this
            ->validate(['taxons' => ['not_existing_taxon']], new CatalogPromotionScope())
            ->shouldReturn(['configuration.taxons' => 'sylius.catalog_promotion_scope.for_taxons.invalid_taxons'])
        ;
    }

    function it_returns_an_empty_array_if_catalog_promotion_scope_is_valid(
        TaxonRepositoryInterface $taxonRepository,
        TaxonInterface $taxon
    ): void {
        $taxonRepository->findOneBy(['code' => 'taxon'])->willReturn($taxon);

        $this->validate(['taxons' => ['taxon']], new CatalogPromotionScope())->shouldReturn([]);
    }
}
