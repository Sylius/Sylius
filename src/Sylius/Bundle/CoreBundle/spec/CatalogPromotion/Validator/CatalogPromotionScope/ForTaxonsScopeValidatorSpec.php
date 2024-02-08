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

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\Validator\CatalogPromotionScope;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionScope\ScopeValidatorInterface;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionScope;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

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

    function it_adds_violation_if_catalog_promotion_scope_has_not_existing_taxons_configured(
        TaxonRepositoryInterface $taxonRepository,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $taxonRepository->findOneBy(['code' => 'not_existing_taxon'])->willReturn(null);

        $executionContext->buildViolation('sylius.catalog_promotion_scope.for_taxons.invalid_taxons')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('configuration.taxons')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate(['taxons' => ['not_existing_taxon']], new CatalogPromotionScope(), $executionContext);
    }

    function it_does_nothing_if_catalog_promotion_scope_is_valid(
        TaxonRepositoryInterface $taxonRepository,
        ExecutionContextInterface $executionContext,
        TaxonInterface $taxon,
    ): void {
        $taxonRepository->findOneBy(['code' => 'taxon'])->willReturn($taxon);

        $executionContext->buildViolation('sylius.catalog_promotion_scope.for_taxons.invalid_taxons')->shouldNotBeCalled();

        $this->validate(['taxons' => ['taxon']], new CatalogPromotionScope(), $executionContext);
    }
}
