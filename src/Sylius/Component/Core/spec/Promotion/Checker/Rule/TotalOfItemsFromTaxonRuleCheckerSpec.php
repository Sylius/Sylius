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

namespace spec\Sylius\Component\Core\Promotion\Checker\Rule;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

final class TotalOfItemsFromTaxonRuleCheckerSpec extends ObjectBehavior
{
    function let(TaxonRepositoryInterface $taxonRepository): void
    {
        $this->beConstructedWith($taxonRepository);
    }

    function it_implements_a_rule_checker_interface(): void
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_recognizes_a_subject_as_eligible_if_it_has_items_from_configured_taxon_which_has_required_total(
        ChannelInterface $channel,
        TaxonRepositoryInterface $taxonRepository,
        OrderInterface $order,
        OrderItemInterface $compositeBowItem,
        OrderItemInterface $longswordItem,
        OrderItemInterface $reflexBowItem,
        ProductInterface $compositeBow,
        ProductInterface $longsword,
        ProductInterface $reflexBow,
        TaxonInterface $bows
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $order->getItems()->willReturn(new ArrayCollection([
            $compositeBowItem->getWrappedObject(),
            $longswordItem->getWrappedObject(),
            $reflexBowItem->getWrappedObject(),
        ]));

        $taxonRepository->findOneBy(['code' => 'bows'])->willReturn($bows);

        $compositeBowItem->getProduct()->willReturn($compositeBow);
        $compositeBow->hasTaxon($bows)->willReturn(true);
        $compositeBowItem->getTotal()->willReturn(5000);

        $longswordItem->getProduct()->willReturn($longsword);
        $longsword->hasTaxon($bows)->willReturn(false);
        $longswordItem->getTotal()->willReturn(4000);

        $reflexBowItem->getProduct()->willReturn($reflexBow);
        $reflexBow->hasTaxon($bows)->willReturn(true);
        $reflexBowItem->getTotal()->willReturn(9000);

        $this->isEligible($order, ['WEB_US' => ['taxon' => 'bows', 'amount' => 10000]])->shouldReturn(true);
    }

    function it_recognizes_a_subject_as_eligible_if_it_has_items_from_configured_taxon_which_has_total_equal_with_required(
        ChannelInterface $channel,
        OrderInterface $order,
        OrderItemInterface $compositeBowItem,
        OrderItemInterface $reflexBowItem,
        ProductInterface $compositeBow,
        ProductInterface $reflexBow,
        TaxonInterface $bows,
        TaxonRepositoryInterface $taxonRepository
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $order->getItems()->willReturn(new ArrayCollection([$compositeBowItem->getWrappedObject(), $reflexBowItem->getWrappedObject()]));

        $taxonRepository->findOneBy(['code' => 'bows'])->willReturn($bows);

        $compositeBowItem->getProduct()->willReturn($compositeBow);
        $compositeBow->hasTaxon($bows)->willReturn(true);
        $compositeBowItem->getTotal()->willReturn(5000);

        $reflexBowItem->getProduct()->willReturn($reflexBow);
        $reflexBow->hasTaxon($bows)->willReturn(true);
        $reflexBowItem->getTotal()->willReturn(5000);

        $this->isEligible($order, ['WEB_US' => ['taxon' => 'bows', 'amount' => 10000]])->shouldReturn(true);
    }

    function it_does_not_recognize_a_subject_as_eligible_if_items_from_required_taxon_has_too_low_total(
        ChannelInterface $channel,
        OrderInterface $order,
        OrderItemInterface $compositeBowItem,
        OrderItemInterface $longswordItem,
        ProductInterface $compositeBow,
        ProductInterface $longsword,
        TaxonInterface $bows,
        TaxonRepositoryInterface $taxonRepository
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $order->getItems()->willReturn(new ArrayCollection([$compositeBowItem->getWrappedObject(), $longswordItem->getWrappedObject()]));

        $taxonRepository->findOneBy(['code' => 'bows'])->willReturn($bows);

        $compositeBowItem->getProduct()->willReturn($compositeBow);
        $compositeBow->hasTaxon($bows)->willReturn(true);
        $compositeBowItem->getTotal()->willReturn(5000);

        $longswordItem->getProduct()->willReturn($longsword);
        $longsword->hasTaxon($bows)->willReturn(false);
        $longswordItem->getTotal()->willReturn(4000);

        $this->isEligible($order, ['WEB_US' => ['taxon' => 'bows', 'amount' => 10000]])->shouldReturn(false);
    }

    function it_returns_false_if_configuration_is_invalid(
        ChannelInterface $channel,
        OrderInterface $order
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $this->isEligible($order, ['WEB_US' => ['amount' => 4000]])->shouldReturn(false);
        $this->isEligible($order, ['WEB_US' => ['taxon' => 'siege_engines']])->shouldReturn(false);
    }

    function it_returns_false_if_there_is_no_configuration_for_order_channel(
        ChannelInterface $channel,
        OrderInterface $order
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $this->isEligible($order, [])->shouldReturn(false);
    }

    function it_returns_false_if_taxon_with_configured_code_cannot_be_found(
        ChannelInterface $channel,
        OrderInterface $order,
        TaxonRepositoryInterface $taxonRepository
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $taxonRepository->findOneBy(['code' => 'sniper_rifles'])->willReturn(null);

        $this->isEligible($order, ['WEB_US' => ['taxon' => 'sniper_rifles', 'amount' => 1000]])->shouldReturn(false);
    }

    function it_throws_an_exception_if_passed_subject_is_not_order(PromotionSubjectInterface $subject): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('isEligible', [$subject, []])
        ;
    }
}
