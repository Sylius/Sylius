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

namespace spec\Sylius\Component\Core\Promotion\Action;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ShippingPercentageDiscountPromotionActionCommandSpec extends ObjectBehavior
{
    function let(FactoryInterface $adjustmentFactory): void
    {
        $this->beConstructedWith($adjustmentFactory);
    }

    function it_implements_a_promotion_action_interface(): void
    {
        $this->shouldImplement(PromotionActionCommandInterface::class);
    }

    function it_applies_percentage_discount_on_every_shipment(
        FactoryInterface $adjustmentFactory,
        OrderInterface $order,
        PromotionInterface $promotion,
        ShipmentInterface $firstShipment,
        ShipmentInterface $secondShipment,
        AdjustmentInterface $firstAdjustment,
        AdjustmentInterface $secondAdjustment,
    ): void {
        $promotion->getName()->willReturn('Promotion');
        $promotion->getCode()->willReturn('PROMOTION');

        $order->hasShipments()->willReturn(true);
        $order->getShipments()->willReturn(new ArrayCollection([
            $firstShipment->getWrappedObject(),
            $secondShipment->getWrappedObject(),
        ]));

        $firstShipment->getAdjustmentsTotal(AdjustmentInterface::SHIPPING_ADJUSTMENT)->willReturn(400);
        $firstShipment->getAdjustmentsTotal(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT)->willReturn(0);
        $adjustmentFactory->createNew()->willReturn($firstAdjustment, $secondAdjustment);
        $firstAdjustment->setType(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $firstAdjustment->setLabel('Promotion')->shouldBeCalled();
        $firstAdjustment->setOriginCode('PROMOTION')->shouldBeCalled();
        $firstAdjustment->setAmount(-200);
        $firstShipment->addAdjustment($firstAdjustment);

        $secondShipment->getAdjustmentsTotal(AdjustmentInterface::SHIPPING_ADJUSTMENT)->willReturn(600);
        $secondShipment->getAdjustmentsTotal(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT)->willReturn(0);
        $secondAdjustment->setType(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $secondAdjustment->setLabel('Promotion')->shouldBeCalled();
        $secondAdjustment->setOriginCode('PROMOTION')->shouldBeCalled();
        $secondAdjustment->setAmount(-300)->shouldBeCalled();
        $secondShipment->addAdjustment($secondAdjustment)->shouldBeCalled();

        $this->execute($order, ['percentage' => 0.5], $promotion)->shouldReturn(true);
    }

    function it_does_not_apply_discount_if_order_has_no_shipment(
        FactoryInterface $adjustmentFactory,
        OrderInterface $order,
        PromotionInterface $promotion,
    ): void {
        $order->hasShipments()->willReturn(false);
        $order->getShipments()->shouldNotBeCalled();
        $adjustmentFactory->createNew()->shouldNotBeCalled();

        $this->execute($order, ['percentage' => 0.1], $promotion)->shouldReturn(false);
    }

    function it_does_not_apply_discount_if_adjustment_amount_would_be_0(
        FactoryInterface $adjustmentFactory,
        OrderInterface $order,
        PromotionInterface $promotion,
        ShipmentInterface $shipment,
    ): void {
        $order->hasShipments()->willReturn(true);
        $order->getShipments()->willReturn(new ArrayCollection([$shipment->getWrappedObject()]));

        $shipment->getAdjustmentsTotal(AdjustmentInterface::SHIPPING_ADJUSTMENT)->willReturn(0);
        $shipment->getAdjustmentsTotal(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT)->willReturn(0);
        $adjustmentFactory->createNew()->shouldNotBeCalled();

        $this->execute($order, ['percentage' => 0.5], $promotion)->shouldReturn(false);
    }

    function it_throws_exception_if_subject_is_not_an_order(
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject,
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('execute', [$subject, [], $promotion])
        ;
    }

    function it_reverts_adjustments(
        OrderInterface $order,
        AdjustmentInterface $firstAdjustment,
        AdjustmentInterface $secondAdjustment,
        ShipmentInterface $firstShipment,
        ShipmentInterface $secondShipment,
        PromotionInterface $promotion,
    ): void {
        $promotion->getCode()->willReturn('PROMOTION');

        $firstAdjustment->getOriginCode()->willReturn('PROMOTION');
        $secondAdjustment->getOriginCode()->willReturn('OTHER_PROMOTION');

        $order->hasShipments()->willReturn(true);
        $order->getShipments()->willReturn(new ArrayCollection([
            $firstShipment->getWrappedObject(),
            $secondShipment->getWrappedObject(),
        ]));
        $order
            ->getAdjustments(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$firstAdjustment->getWrappedObject(), $secondAdjustment->getWrappedObject()]))
        ;

        $order->removeAdjustment($firstAdjustment)->shouldBeCalled();
        $order->removeAdjustment($secondAdjustment)->shouldNotBeCalled();

        $firstShipment
            ->getAdjustments(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$firstAdjustment->getWrappedObject()]))
        ;
        $firstShipment->removeAdjustment($firstAdjustment)->shouldBeCalled();

        $secondShipment
            ->getAdjustments(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$secondAdjustment->getWrappedObject()]))
        ;
        $secondShipment->removeAdjustment($secondAdjustment)->shouldNotBeCalled();

        $this->revert($order, [], $promotion);
    }

    function it_does_not_revert_adjustments_if_order_has_no_shipment(OrderInterface $order, PromotionInterface $promotion): void
    {
        $order->hasShipments()->willReturn(false);
        $order->getShipments()->shouldNotBeCalled();

        $this->revert($order, [], $promotion);
    }

    function it_throws_an_exception_while_reverting_subject_is_not_an_order(
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject,
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('revert', [$subject, [], $promotion])
        ;
    }
}
