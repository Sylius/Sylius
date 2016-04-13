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
use Sylius\Component\Core\Promotion\Checker\ContainsTaxonRuleChecker;
use Sylius\Component\Promotion\Checker\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

/**
 * @mixin ContainsTaxonRuleChecker
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ContainsTaxonRuleCheckerSpec extends ObjectBehavior
{
    function let(TaxonRepositoryInterface $taxonRepository)
    {
        $this->beConstructedWith($taxonRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Checker\ContainsTaxonRuleChecker');
    }

    function it_implements_rule_checker_interface()
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_returns_true_if_order_has_required_number_of_products_from_configured_taxon(
        OrderInterface $order,
        OrderItemInterface $compositeBowItem,
        OrderItemInterface $longbowItem,
        ProductInterface $compositeBow,
        ProductInterface $longbow,
        TaxonInterface $bows,
        TaxonRepositoryInterface $taxonRepository
    ) {
        $order
            ->getItems()
            ->willReturn(new \ArrayIterator([$compositeBowItem->getWrappedObject(), $longbowItem->getWrappedObject()]))
        ;

        $taxonRepository->findOneBy(['code' => 'bows'])->willReturn($bows);

        $compositeBowItem->getProduct()->willReturn($compositeBow);
        $compositeBow->hasTaxon($bows)->willReturn(true);
        $compositeBowItem->getQuantity()->willReturn(4);

        $longbowItem->getProduct()->willReturn($longbow);
        $longbow->hasTaxon($bows)->willReturn(true);
        $longbowItem->getQuantity()->willReturn(5);

        $this->isEligible($order, ['taxon' => 'bows', 'count' => 5])->shouldReturn(true);
    }

    function it_returns_false_if_an_order_does_not_have_the_required_number_of_products_from_a_configured_taxon(
        OrderInterface $order,
        OrderItemInterface $compositeBowItem,
        OrderItemInterface $longbowItem,
        ProductInterface $compositeBow,
        ProductInterface $longbow,
        TaxonInterface $bows,
        TaxonRepositoryInterface $taxonRepository
    ) {
        $order
            ->getItems()
            ->willReturn(new \ArrayIterator([$compositeBowItem->getWrappedObject(), $longbowItem->getWrappedObject()]))
        ;

        $taxonRepository->findOneBy(['code' => 'bows'])->willReturn($bows);

        $compositeBowItem->getProduct()->willReturn($compositeBow);
        $compositeBow->hasTaxon($bows)->willReturn(true);
        $compositeBowItem->getQuantity()->willReturn(4);

        $longbowItem->getProduct()->willReturn($longbow);
        $longbow->hasTaxon($bows)->willReturn(true);
        $longbowItem->getQuantity()->willReturn(5);

        $this->isEligible($order, ['taxon' => 'bows', 'count' => 15])->shouldReturn(false);
    }

    function it_does_not_check_item_if_its_product_has_no_required_taxon(
        OrderInterface $order,
        OrderItemInterface $compositeBowItem,
        OrderItemInterface $longswordItem,
        ProductInterface $compositeBow,
        ProductInterface $longsword,
        TaxonInterface $bows,
        TaxonRepositoryInterface $taxonRepository
    ) {
        $order
            ->getItems()
            ->willReturn(new \ArrayIterator([$compositeBowItem->getWrappedObject(), $longswordItem->getWrappedObject()]))
        ;

        $taxonRepository->findOneBy(['code' => 'bows'])->willReturn($bows);

        $compositeBowItem->getProduct()->willReturn($compositeBow);
        $compositeBow->hasTaxon($bows)->willReturn(true);
        $compositeBowItem->getQuantity()->willReturn(4);

        $longswordItem->getProduct()->willReturn($longsword);
        $longsword->hasTaxon($bows)->willReturn(false);
        $longswordItem->getQuantity()->shouldNotBeCalled();

        $this->isEligible($order, ['taxon' => 'bows', 'count' => 5])->shouldReturn(false);
    }

    function it_returns_false_if_configuration_is_invalid(OrderInterface $order)
    {
        $this->isEligible($order, ['taxon' => 'bows'])->shouldReturn(false);
        $this->isEligible($order, ['count' => 10])->shouldReturn(false);
        $this->isEligible($order, [])->shouldReturn(false);
    }

    function it_returns_false_if_taxon_with_configured_code_does_not_exist(
        OrderInterface $order,
        TaxonRepositoryInterface $taxonRepository
    ) {
        $taxonRepository->findOneBy(['code' => 'bows'])->willReturn(null);

        $this->isEligible($order, ['taxon' => 'bows', 'count' => 10])->shouldReturn(false);
    }

    function it_throws_exception_if_the_promotion_subject_is_not_an_order(PromotionSubjectInterface $subject)
    {
        $this
            ->shouldThrow(new UnexpectedTypeException($subject->getWrappedObject(), OrderInterface::class))
            ->during('isEligible', [$subject, []])
        ;
    }

    function it_has_configuration_type()
    {
        $this->getConfigurationFormType()->shouldReturn('sylius_promotion_rule_contains_taxon_configuration');
    }
}
