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

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Inventory\Updater\OnHoldQuantityUpdater;
use Sylius\Component\Core\Inventory\Updater\OrderQuantityUpdaterInterface;
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

    function it_implements_order_quantity_updater_interface()
    {
        $this->shouldImplement(OrderQuantityUpdaterInterface::class);
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

        $order->getItems()->willReturn([$orderItem]);

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
        $order->getItems()->willReturn([$orderItem]);

        $productVariant->setOnHold(5)->shouldBeCalled();

        $this->decrease($order);
    }

    function it_increase_on_hold_quantity_on_various_variants(
        OrderInterface $order,
        OrderItemInterface $orderItem1,
        OrderItemInterface $orderItem2,
        OrderItemInterface $orderItem3,
        ProductVariantInterface $productVariant1,
        ProductVariantInterface $productVariant2,
        ProductVariantInterface $productVariant3
    ) {
        $productVariant1->getOnHold()->willReturn(10);
        $productVariant1->isTracked()->willReturn(true);

        $productVariant2->getOnHold()->willReturn(1);
        $productVariant2->isTracked()->willReturn(true);

        $productVariant3->getOnHold()->willReturn(0);
        $productVariant3->isTracked()->willReturn(false);

        $orderItem1->getVariant()->willReturn($productVariant1);
        $orderItem1->getQuantity()->willReturn(19);

        $orderItem2->getVariant()->willReturn($productVariant2);
        $orderItem2->getQuantity()->willReturn(30);

        $orderItem3->getVariant()->willReturn($productVariant3);
        $orderItem3->getQuantity()->willReturn(10);

        $order->getItems()->willReturn([$orderItem1, $orderItem2, $orderItem3]);

        $productVariant1->setOnHold(29)->shouldBeCalled();
        $productVariant2->setOnHold(31)->shouldBeCalled();
        $productVariant3->setOnHold(10)->shouldNotBeCalled();

        $this->increase($order);
    }

    function it_decrease_on_hold_quantity_on_various_variants(
        OrderInterface $order,
        OrderItemInterface $orderItem1,
        OrderItemInterface $orderItem2,
        OrderItemInterface $orderItem3,
        ProductVariantInterface $productVariant1,
        ProductVariantInterface $productVariant2,
        ProductVariantInterface $productVariant3
    ) {
        $productVariant1->getOnHold()->willReturn(10);
        $productVariant1->isTracked()->willReturn(true);
        $productVariant1->getName()->willReturn('t-shirt1');

        $productVariant2->getOnHold()->willReturn(20);
        $productVariant2->isTracked()->willReturn(true);
        $productVariant2->getName()->willReturn('t-shirt2');

        $productVariant3->getOnHold()->willReturn(0);
        $productVariant3->isTracked()->willReturn(false);
        $productVariant3->getName()->willReturn('t-shirt3');

        $orderItem1->getVariant()->willReturn($productVariant1);
        $orderItem1->getQuantity()->willReturn(10);

        $orderItem2->getVariant()->willReturn($productVariant2);
        $orderItem2->getQuantity()->willReturn(15);

        $orderItem3->getVariant()->willReturn($productVariant3);
        $orderItem3->getQuantity()->willReturn(10);

        $order->getItems()->willReturn([$orderItem1, $orderItem2, $orderItem3]);

        $productVariant1->setOnHold(0)->shouldBeCalled();
        $productVariant2->setOnHold(5)->shouldBeCalled();
        $productVariant3->setOnHold(-10)->shouldNotBeCalled();

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
        $order->getItems()->willReturn([$orderItem]);

        $productVariant->setOnHold(-5)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('decrease', [$order]);
    }
}
