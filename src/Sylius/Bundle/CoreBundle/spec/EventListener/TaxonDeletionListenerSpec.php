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

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class TaxonDeletionListenerSpec extends ObjectBehavior
{
    function let(RepositoryInterface $promotionRuleRepository, EntityManagerInterface $manager): void
    {
        $this->beConstructedWith($promotionRuleRepository, $manager);
    }

    function it_removes_rules_that_using_deleted_taxon(
        RepositoryInterface $promotionRuleRepository,
        EntityManagerInterface $manager,
        GenericEvent $event,
        TaxonInterface $taxon,
        PromotionRuleInterface $firstPromotionRule,
        PromotionRuleInterface $secondPromotionRule
    ): void {
        $event->getSubject()->willReturn($taxon);
        $taxon->getCode()->willReturn('toys');

        $promotionRuleRepository
            ->findBy(['type' => 'has_taxon'])
            ->willReturn([$firstPromotionRule, $secondPromotionRule])
        ;
        $firstPromotionRule->getConfiguration()->willReturn(['taxons' => ['mugs', 'toys']]);
        $secondPromotionRule->getConfiguration()->willReturn(['taxons' => ['mugs']]);

        $firstPromotionRule->setConfiguration(['taxons' => ['mugs']])->shouldBeCalled();
        $secondPromotionRule->setConfiguration(Argument::any())->shouldNotBeCalled();

        $promotionRuleRepository->findBy(['type' => 'total_of_items_from_taxon'])->willReturn([]);

        $manager->flush()->shouldBeCalled();

        $this->removeTaxonFromPromotionRules($event);
    }

    function it_removes_deleted_taxon_from_rules_configurations(
        RepositoryInterface $promotionRuleRepository,
        EntityManagerInterface $manager,
        GenericEvent $event,
        TaxonInterface $taxon,
        PromotionRuleInterface $firstPromotionRule,
        PromotionRuleInterface $secondPromotionRule
    ): void {
        $event->getSubject()->willReturn($taxon);
        $taxon->getCode()->willReturn('toys');

        $promotionRuleRepository->findBy(['type' => 'has_taxon'])->willReturn([]);

        $promotionRuleRepository
            ->findBy(['type' => 'total_of_items_from_taxon'])
            ->willReturn([$firstPromotionRule, $secondPromotionRule])
        ;
        $firstPromotionRule->getConfiguration()->willReturn(['web' => ['taxon' => 'mugs', 'amount' => 500]]);
        $secondPromotionRule->getConfiguration()->willReturn(['web' => ['taxon' => 'toys', 'amount' => 300]]);

        $promotionRuleRepository->remove($firstPromotionRule)->shouldNotBeCalled();
        $promotionRuleRepository->remove($secondPromotionRule)->shouldBeCalled();

        $manager->flush()->shouldBeCalled();

        $this->removeTaxonFromPromotionRules($event);
    }

    function it_does_nothing_if_there_is_no_rule_with_given_types(
        RepositoryInterface $promotionRuleRepository,
        GenericEvent $event,
        TaxonInterface $taxon
    ): void {
        $event->getSubject()->willReturn($taxon);
        $taxon->getCode()->willReturn('toys');

        $promotionRuleRepository->findBy(['type' => 'has_taxon'])->willReturn([]);
        $promotionRuleRepository->findBy(['type' => 'total_of_items_from_taxon'])->willReturn([]);

        $this->removeTaxonFromPromotionRules($event);
    }
}
