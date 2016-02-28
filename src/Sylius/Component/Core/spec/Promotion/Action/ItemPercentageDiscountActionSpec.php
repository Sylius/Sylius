<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Promotion\Action;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Promotion\Action\DiscountAction;
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ItemPercentageDiscountActionSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $adjustmentFactory,
        OriginatorInterface $originator,
        IntegerDistributorInterface $distributor
    ) {
        $this->beConstructedWith($adjustmentFactory, $originator, $distributor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Action\ItemPercentageDiscountAction');
    }

    function it_is_discount_action()
    {
        $this->shouldHaveType(DiscountAction::class);
    }

    function it_applies_percentage_discount_on_every_unit_in_order(
        $adjustmentFactory,
        $distributor,
        $originator,
        AdjustmentInterface $promotionAdjustment1,
        AdjustmentInterface $promotionAdjustment2,
        Collection $items,
        Collection $units,
        \Iterator $itemsIterator,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        PromotionInterface $promotion
    ) {
        $order->getItems()->willReturn($items);
        $items->getIterator()->willReturn($itemsIterator);
        $itemsIterator->rewind()->shouldBeCalled();
        $itemsIterator->valid()->willReturn(true, false);
        $itemsIterator->current()->willReturn($orderItem);
        $itemsIterator->next()->shouldBeCalled();

        $orderItem->getUnits()->willReturn($units);
        $units->count()->willReturn(2);

        $units->first()->willReturn($unit1);
        $units->current()->willReturn($unit1, $unit2);
        $units->next()->shouldBeCalled();

        $orderItem->getTotal()->willReturn(1000);
        $distributor->distribute(200, 2)->willReturn([100, 100]);

        $promotion->getDescription()->willReturn('Test description');

        $adjustmentFactory->createNew()->willReturn($promotionAdjustment1, $promotionAdjustment2);

        $promotionAdjustment1->setType(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $promotionAdjustment1->setLabel('Test description')->shouldBeCalled();
        $promotionAdjustment1->setAmount(-100)->shouldBeCalled();

        $originator->setOrigin($promotionAdjustment1, $promotion)->shouldBeCalled();

        $promotionAdjustment2->setType(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $promotionAdjustment2->setLabel('Test description')->shouldBeCalled();
        $promotionAdjustment2->setAmount(-100)->shouldBeCalled();

        $originator->setOrigin($promotionAdjustment2, $promotion)->shouldBeCalled();

        $this->execute($order, ['percentage' => 0.2], $promotion);
    }

    function it_throws_exception_if_passed_subject_is_not_order(
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ) {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('execute', [$subject, ['percentage' => 0.2], $promotion])
        ;
    }

    function it_has_configuration_form_type()
    {
        $this->getConfigurationFormType()->shouldReturn('sylius_promotion_action_percentage_discount_configuration');
    }
}
