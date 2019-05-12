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

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class HasTaxonRuleUpdaterSpec extends ObjectBehavior
{
    function let(RepositoryInterface $promotionRuleRepository, EntityManagerInterface $manager): void
    {
        $this->beConstructedWith($promotionRuleRepository, $manager);
    }

    function it_removes_deleted_taxon_from_rules_configurations(
        RepositoryInterface $promotionRuleRepository,
        EntityManagerInterface $manager,
        PromotionRuleInterface $firstPromotionRule,
        PromotionRuleInterface $secondPromotionRule,
        PromotionInterface $promotion,
        TaxonInterface $taxon
    ): void {
        $taxon->getCode()->willReturn('toys');

        $promotionRuleRepository
            ->findBy(['type' => 'has_taxon'])
            ->willReturn([$firstPromotionRule, $secondPromotionRule])
        ;
        $firstPromotionRule->getConfiguration()->willReturn(['taxons' => ['mugs', 'toys']]);
        $secondPromotionRule->getConfiguration()->willReturn(['taxons' => ['mugs']]);

        $firstPromotionRule->getPromotion()->willReturn($promotion);
        $promotion->getCode()->willReturn('christmas');

        $firstPromotionRule->setConfiguration(['taxons' => ['mugs']])->shouldBeCalled();
        $secondPromotionRule->setConfiguration(Argument::any())->shouldNotBeCalled();

        $manager->flush()->shouldBeCalled();

        $this->updateAfterDeletingTaxon($taxon)->shouldReturn(['christmas']);
    }
}
