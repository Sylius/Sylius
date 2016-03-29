<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Promotion\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Checker\TotalOfItemsFromTaxonRuleChecker;
use Sylius\Component\Promotion\Checker\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

/**
 * @mixin TotalOfItemsFromTaxonRuleChecker
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class TotalOfItemsFromTaxonRuleCheckerSpec extends ObjectBehavior
{
    function let(TaxonRepositoryInterface $taxonRepository)
    {
        $this->beConstructedWith($taxonRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Checker\TotalOfItemsFromTaxonRuleChecker');
    }

    function it_implements_rule_checker_interface()
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_recognizes_subject_as_eligible_if_it_has_items_from_configured_taxon_which_has_required_total(
        OrderInterface $order,
        OrderItemInterface $compositeBowItem,
        OrderItemInterface $longswordItem,
        OrderItemInterface $reflexBowItem,
        ProductInterface $compositeBow,
        ProductInterface $longsword,
        ProductInterface $reflexBow,
        TaxonInterface $bows,
        TaxonRepositoryInterface $taxonRepository
    ) {
        $order
            ->getItems()
            ->willReturn(
                new \ArrayIterator([$compositeBowItem->getWrappedObject(), $longswordItem->getWrappedObject(), $reflexBowItem->getWrappedObject()])
            )
        ;

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

        $this->isEligible($order, ['taxon' => 'bows', 'amount' => 10000])->shouldReturn(true);
    }

    function it_recognizes_subject_as_eligible_if_it_has_items_from_configured_taxon_which_has_total_equal_with_required(
        OrderInterface $order,
        OrderItemInterface $compositeBowItem,
        OrderItemInterface $reflexBowItem,
        ProductInterface $compositeBow,
        ProductInterface $reflexBow,
        TaxonInterface $bows,
        TaxonRepositoryInterface $taxonRepository
    ) {
        $order->getItems()->willReturn(new \ArrayIterator([$compositeBowItem->getWrappedObject(), $reflexBowItem->getWrappedObject()]));

        $taxonRepository->findOneBy(['code' => 'bows'])->willReturn($bows);

        $compositeBowItem->getProduct()->willReturn($compositeBow);
        $compositeBow->hasTaxon($bows)->willReturn(true);
        $compositeBowItem->getTotal()->willReturn(5000);

        $reflexBowItem->getProduct()->willReturn($reflexBow);
        $reflexBow->hasTaxon($bows)->willReturn(true);
        $reflexBowItem->getTotal()->willReturn(5000);

        $this->isEligible($order, ['taxon' => 'bows', 'amount' => 10000])->shouldReturn(true);
    }

    function it_does_not_recognize_subject_as_eligible_if_items_from_required_taxon_has_too_low_total(
        OrderInterface $order,
        OrderItemInterface $compositeBowItem,
        OrderItemInterface $longswordItem,
        ProductInterface $compositeBow,
        ProductInterface $longsword,
        TaxonInterface $bows,
        TaxonRepositoryInterface $taxonRepository
    ) {
        $order->getItems()->willReturn(new \ArrayIterator([$compositeBowItem->getWrappedObject(), $longswordItem->getWrappedObject()]));

        $taxonRepository->findOneBy(['code' => 'bows'])->willReturn($bows);

        $compositeBowItem->getProduct()->willReturn($compositeBow);
        $compositeBow->hasTaxon($bows)->willReturn(true);
        $compositeBowItem->getTotal()->willReturn(5000);

        $longswordItem->getProduct()->willReturn($longsword);
        $longsword->hasTaxon($bows)->willReturn(false);
        $longswordItem->getTotal()->willReturn(4000);

        $this->isEligible($order, ['taxon' => 'bows', 'amount' => 10000])->shouldReturn(false);
    }

    function it_returns_false_if_configuration_is_invalid(OrderInterface $order)
    {
        $this->isEligible($order, ['amount' => 4000])->shouldReturn(false);
        $this->isEligible($order, ['taxon' => 'siege_engines'])->shouldReturn(false);
        $this->isEligible($order, [])->shouldReturn(false);
    }

    function it_throws_exception_if_passed_subject_is_not_order(PromotionSubjectInterface $subject)
    {
        $this
            ->shouldThrow(new UnexpectedTypeException($subject->getWrappedObject(), OrderInterface::class))
            ->during('isEligible', [$subject, []])
        ;
    }

    function it_returns_false_if_taxon_with_configured_code_cannot_be_found(
        OrderInterface $order,
        TaxonRepositoryInterface $taxonRepository
    ) {
        $taxonRepository->findOneBy(['code' => 'sniper_rifles'])->willReturn(null);

        $this->isEligible($order, ['taxon' => 'sniper_rifles', 'amount' => 1000])->shouldReturn(false);
    }

    function it_has_configuration_form_type()
    {
        $this->getConfigurationFormType()->shouldReturn(null);
    }
}
