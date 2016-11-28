<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Promotion\Checker\Rule;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\ChannelAwareRuleCheckerInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\TotalOfItemsFromTaxonRuleChecker;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class TotalOfItemsFromTaxonRuleCheckerSpec extends ObjectBehavior
{
    function let(TaxonRepositoryInterface $taxonRepository)
    {
        $this->beConstructedWith($taxonRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TotalOfItemsFromTaxonRuleChecker::class);
    }

    function it_implements_a_rule_checker_interface()
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_implements_channel_aware_rule_checker_interface()
    {
        $this->shouldImplement(ChannelAwareRuleCheckerInterface::class);
    }

    function it_recognizes_a_subject_as_eligible_if_it_has_items_from_configured_taxon_which_has_required_total(
        TaxonRepositoryInterface $taxonRepository,
        OrderInterface $order,
        OrderItemInterface $compositeBowItem,
        OrderItemInterface $longswordItem,
        OrderItemInterface $reflexBowItem,
        ProductInterface $compositeBow,
        ProductInterface $longsword,
        ProductInterface $reflexBow,
        ProductTaxonInterface $bowsProductTaxon,
        TaxonInterface $bows
    ) {
        $order->getItems()->willReturn([$compositeBowItem, $longswordItem, $reflexBowItem]);

        $taxonRepository->findOneBy(['code' => 'bows'])->willReturn($bows);

        $compositeBowItem->getProduct()->willReturn($compositeBow);
        $compositeBow->filterProductTaxonsByTaxon($bows)->willReturn(new ArrayCollection([$bowsProductTaxon]));
        $compositeBowItem->getTotal()->willReturn(5000);

        $longswordItem->getProduct()->willReturn($longsword);
        $longsword->filterProductTaxonsByTaxon($bows)->willReturn(new ArrayCollection([]));
        $longswordItem->getTotal()->willReturn(4000);

        $reflexBowItem->getProduct()->willReturn($reflexBow);
        $reflexBow->filterProductTaxonsByTaxon($bows)->willReturn(new ArrayCollection([$bowsProductTaxon]));
        $reflexBowItem->getTotal()->willReturn(9000);

        $this->isEligible($order, ['taxon' => 'bows', 'amount' => 10000])->shouldReturn(true);
    }

    function it_recognizes_a_subject_as_eligible_if_it_has_items_from_configured_taxon_which_has_total_equal_with_required(
        TaxonRepositoryInterface $taxonRepository,
        OrderInterface $order,
        OrderItemInterface $compositeBowItem,
        OrderItemInterface $reflexBowItem,
        ProductInterface $compositeBow,
        ProductInterface $reflexBow,
        ProductTaxonInterface $bowsProductTaxon,
        TaxonInterface $bows
    ) {
        $order->getItems()->willReturn([$compositeBowItem, $reflexBowItem]);

        $taxonRepository->findOneBy(['code' => 'bows'])->willReturn($bows);

        $compositeBowItem->getProduct()->willReturn($compositeBow);
        $compositeBow->filterProductTaxonsByTaxon($bows)->willReturn(new ArrayCollection([$bowsProductTaxon]));
        $compositeBowItem->getTotal()->willReturn(5000);

        $reflexBowItem->getProduct()->willReturn($reflexBow);
        $reflexBow->filterProductTaxonsByTaxon($bows)->willReturn(new ArrayCollection([$bowsProductTaxon]));
        $reflexBowItem->getTotal()->willReturn(5000);

        $this->isEligible($order, ['taxon' => 'bows', 'amount' => 10000])->shouldReturn(true);
    }

    function it_does_not_recognize_a_subject_as_eligible_if_items_from_required_taxon_has_too_low_total(
        TaxonRepositoryInterface $taxonRepository,
        OrderInterface $order,
        OrderItemInterface $compositeBowItem,
        OrderItemInterface $longswordItem,
        ProductInterface $compositeBow,
        ProductInterface $longsword,
        ProductTaxonInterface $bowsProductTaxon,
        TaxonInterface $bows
    ) {
        $order->getItems()->willReturn([$compositeBowItem, $longswordItem]);

        $taxonRepository->findOneBy(['code' => 'bows'])->willReturn($bows);

        $compositeBowItem->getProduct()->willReturn($compositeBow);
        $compositeBow->filterProductTaxonsByTaxon($bows)->willReturn(new ArrayCollection([$bowsProductTaxon]));
        $compositeBowItem->getTotal()->willReturn(5000);

        $longswordItem->getProduct()->willReturn($longsword);
        $longsword->filterProductTaxonsByTaxon($bows)->willReturn(new ArrayCollection([]));
        $longswordItem->getTotal()->willReturn(4000);

        $this->isEligible($order, ['taxon' => 'bows', 'amount' => 10000])->shouldReturn(false);
    }

    function it_returns_false_if_configuration_is_invalid(OrderInterface $order)
    {
        $this->isEligible($order, ['amount' => 4000])->shouldReturn(false);
        $this->isEligible($order, ['taxon' => 'siege_engines'])->shouldReturn(false);
        $this->isEligible($order, [])->shouldReturn(false);
    }

    function it_throws_an_exception_if_passed_subject_is_not_order(PromotionSubjectInterface $subject)
    {
        $this
            ->shouldThrow(new UnexpectedTypeException($subject->getWrappedObject(), OrderInterface::class))
            ->during('isEligible', [$subject, []])
        ;
    }

    function it_returns_false_if_taxon_with_configured_code_cannot_be_found(
        TaxonRepositoryInterface $taxonRepository,
        OrderInterface $order
    ) {
        $taxonRepository->findOneBy(['code' => 'sniper_rifles'])->willReturn(null);

        $this->isEligible($order, ['taxon' => 'sniper_rifles', 'amount' => 1000])->shouldReturn(false);
    }
}
