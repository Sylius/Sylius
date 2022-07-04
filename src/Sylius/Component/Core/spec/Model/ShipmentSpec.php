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

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\Shipment as BaseShipment;

final class ShipmentSpec extends ObjectBehavior
{
    function it_implements_a_shipment_interface(): void
    {
        $this->shouldImplement(ShipmentInterface::class);
    }

    function it_extends_a_base_shipment(): void
    {
        $this->shouldHaveType(BaseShipment::class);
    }

    function it_does_not_belong_to_an_order_by_default(): void
    {
        $this->getOrder()->shouldReturn(null);
    }

    function it_allows_attaching_itself_to_an_order(OrderInterface $order): void
    {
        $this->setOrder($order);
        $this->getOrder()->shouldReturn($order);
    }

    function it_allows_detaching_itself_from_an_order(OrderInterface $order): void
    {
        $this->setOrder($order);
        $this->getOrder()->shouldReturn($order);

        $this->setOrder(null);
        $this->getOrder()->shouldReturn(null);
    }

    function it_adds_and_removes_adjustments(AdjustmentInterface $adjustment, OrderInterface $order): void
    {
        $this->setOrder($order);

        $adjustment->isNeutral()->willReturn(true);
        $adjustment->setShipment($this)->shouldBeCalled();

        $this->addAdjustment($adjustment);
        $this->hasAdjustment($adjustment)->shouldReturn(true);

        $adjustment->setShipment(null)->shouldBeCalled();
        $adjustment->isLocked()->willReturn(false);

        $this->removeAdjustment($adjustment);
        $this->hasAdjustment($adjustment)->shouldReturn(false);

        $order->recalculateAdjustmentsTotal()->shouldBeCalledTimes(2);
    }

    function it_does_not_remove_adjustment_when_it_is_locked(
        AdjustmentInterface $adjustment,
        OrderInterface $order,
    ): void {
        $this->setOrder($order);

        $adjustment->isNeutral()->willReturn(true);
        $adjustment->setShipment($this)->shouldBeCalled();

        $this->addAdjustment($adjustment);

        $adjustment->setShipment(null)->shouldNotBeCalled();
        $adjustment->isLocked()->willReturn(true);

        $this->removeAdjustment($adjustment);
        $this->hasAdjustment($adjustment)->shouldReturn(true);

        $order->recalculateAdjustmentsTotal()->shouldBeCalledOnce();
    }

    function it_has_correct_adjustments_total_after_adjustment_add_and_remove(
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $adjustment2,
        AdjustmentInterface $adjustment3,
        AdjustmentInterface $adjustment4,
        OrderInterface $order,
    ): void {
        $this->setOrder($order);

        $adjustment1->isNeutral()->willReturn(false);
        $adjustment1->setShipment($this)->shouldBeCalled();
        $adjustment1->isLocked()->willReturn(false);
        $adjustment1->setShipment(null)->shouldBeCalled();
        $adjustment1->getAmount()->willReturn(100);

        $adjustment2->isNeutral()->willReturn(false);
        $adjustment2->setShipment($this)->shouldBeCalled();
        $adjustment2->getAmount()->willReturn(50);

        $adjustment3->isNeutral()->willReturn(false);
        $adjustment3->setShipment($this)->shouldBeCalled();
        $adjustment3->getAmount()->willReturn(250);

        $adjustment4->isNeutral()->willReturn(true);
        $adjustment4->setShipment($this)->shouldBeCalled();
        $adjustment4->getAmount()->willReturn(150);

        $this->addAdjustment($adjustment1);
        $this->addAdjustment($adjustment2);
        $this->addAdjustment($adjustment3);
        $this->addAdjustment($adjustment4);

        $this->removeAdjustment($adjustment1);

        $order->recalculateAdjustmentsTotal()->shouldBeCalledTimes(5);

        $this->getAdjustmentsTotal()->shouldReturn(300);
    }
}
