<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Inventory\Updater;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Inventory\Updater\DecreasingQuantityUpdaterInterface;
use Sylius\Component\Core\Inventory\Updater\IncreasingQuantityUpdaterInterface;
use Sylius\Component\Core\Inventory\Updater\OnHoldQuantityUpdater;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 * @mixin OnHoldQuantityUpdater
 *
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class OnHoldQuantityUpdaterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(OnHoldQuantityUpdater::class);
    }

    function it_is_a_increasing_quantity_updater()
    {
        $this->shouldImplement(IncreasingQuantityUpdaterInterface::class);
    }

    function it_is_a_decreasing_quantity_updater()
    {
        $this->shouldImplement(DecreasingQuantityUpdaterInterface::class);
    }

    function it_increase_on_hold_quantity(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant
    ) {
        $productVariant->getOnHold()->willReturn(3);
        $productVariant->isTracked()->willReturn(true);

        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getQuantity()->willReturn(10);

        $orderItems = new ArrayCollection();
        $orderItems->add($orderItem->getWrappedObject());
        $order->getItems()->willReturn($orderItems);

        $productVariant->setOnHold(13)->shouldBeCalled();

        $this->increase($order);
    }

    function it_decrease_on_hold_quantity(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant
    ) {
        $productVariant->getOnHold()->willReturn(10);
        $productVariant->isTracked()->willReturn(true);
        $productVariant->getName()->willReturn('t-shirt');

        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getQuantity()->willReturn(5);
        $orderItems = new ArrayCollection();
        $orderItems->add($orderItem->getWrappedObject());
        $order->getItems()->willReturn($orderItems);

        $productVariant->setOnHold(5)->shouldBeCalled();

        $this->decrease($order);
    }

    function it_throws_invalid_argument_exception_if_on_hold_quantity_to_decrease_will_be_below_zero(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant
    ) {
        $productVariant->getOnHold()->willReturn(5);
        $productVariant->isTracked()->willReturn(true);
        $productVariant->getName()->willReturn('t-shirt');

        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getQuantity()->willReturn(10);
        $orderItems = new ArrayCollection();
        $orderItems->add($orderItem->getWrappedObject());
        $order->getItems()->willReturn($orderItems);

        $productVariant->setOnHold(-5)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('decrease', [$order]);
    }
}
