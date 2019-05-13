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

namespace spec\Sylius\Component\Core\Promotion\Updater\Rule;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class TotalOfItemsFromTaxonRuleUpdaterSpec extends ObjectBehavior
{
    function let(RepositoryInterface $promotionRuleRepository): void
    {
        $this->beConstructedWith($promotionRuleRepository);
    }

    function it_removes_rules_that_using_deleted_taxon(
        RepositoryInterface $promotionRuleRepository,
        PromotionRuleInterface $firstPromotionRule,
        PromotionRuleInterface $secondPromotionRule,
        PromotionInterface $promotion,
        TaxonInterface $taxon
    ): void {
        $taxon->getCode()->willReturn('toys');

        $promotionRuleRepository
            ->findBy(['type' => 'total_of_items_from_taxon'])
            ->willReturn([$firstPromotionRule, $secondPromotionRule])
        ;
        $firstPromotionRule->getConfiguration()->willReturn(['web' => ['taxon' => 'mugs', 'amount' => 500]]);
        $secondPromotionRule->getConfiguration()->willReturn(['web' => ['taxon' => 'toys', 'amount' => 300]]);

        $secondPromotionRule->getPromotion()->willReturn($promotion);
        $promotion->getCode()->willReturn('christmas');

        $promotionRuleRepository->remove($firstPromotionRule)->shouldNotBeCalled();
        $promotionRuleRepository->remove($secondPromotionRule)->shouldBeCalled();

        $this->updateAfterDeletingTaxon($taxon)->shouldReturn(['christmas']);
    }
}
