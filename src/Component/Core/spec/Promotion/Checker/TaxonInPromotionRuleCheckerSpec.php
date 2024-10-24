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

namespace spec\Sylius\Component\Core\Promotion\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Checker\TaxonInPromotionRuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class TaxonInPromotionRuleCheckerSpec extends ObjectBehavior
{
    function let(RepositoryInterface $promotionRuleRepository)
    {
        $this->beConstructedWith($promotionRuleRepository);
    }

    function it_implements_a_total_of_items_from_taxon_promotion_rule_applied_checker_interface(): void
    {
        $this->shouldImplement(TaxonInPromotionRuleCheckerInterface::class);
    }

    function it_checks_if_promotion_rule_is_applied_with_taxon(
        RepositoryInterface $promotionRuleRepository,
        PromotionRuleInterface $promotionRule,
        TaxonInterface $taxon,
    ): void {
        $promotionRuleRepository->findBy(['type' => 'total_of_items_from_taxon'])->willReturn([$promotionRule]);

        $promotionRule->getConfiguration()->willReturn(['FASHION_WEB' => ['taxon' => 'sample_taxon_code']]);

        $taxon->getCode()->willReturn('sample_taxon_code');

        $this->isInUse($taxon)->shouldReturn(true);
    }

    function it_returns_false_when_promotion_rule_is_not_applied_with_taxon(
        RepositoryInterface $promotionRuleRepository,
        PromotionRuleInterface $promotionRule,
        TaxonInterface $taxon,
    ): void {
        $promotionRuleRepository->findBy(['type' => 'total_of_items_from_taxon'])->willReturn([$promotionRule]);

        $promotionRule->getConfiguration()->willReturn(['FASHION_WEB' => ['taxon' => 'sample_taxon_code']]);

        $taxon->getCode()->willReturn('different_taxon_code');

        $this->isInUse($taxon)->shouldReturn(false);
    }

    function it_returns_false_when_no_promotion_rules_are_found(
        RepositoryInterface $promotionRuleRepository,
        TaxonInterface $taxon,
    ): void {
        $promotionRuleRepository->findBy(['type' => 'total_of_items_from_taxon'])->willReturn([]);

        $this->isInUse($taxon)->shouldReturn(false);
    }
}
