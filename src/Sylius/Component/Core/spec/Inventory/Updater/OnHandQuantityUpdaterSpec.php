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

use Sylius\Component\Core\Inventory\Updater\OnHandQuantityUpdater;
use Sylius\Component\Core\Inventory\Updater\OrderQuantityUpdaterInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use PhpSpec\ObjectBehavior;

/**
 * @mixin OnHandQuantityUpdater
 *
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class OnHandQuantityUpdaterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(OnHandQuantityUpdater::class);
    }

    function it_implements_order_quantity_updater_interface()
    {
        $this->shouldImplement(OrderQuantityUpdaterInterface::class);
    }

    function it_decreases_quantity_of_product_variant_available_on_hand(
        OrderInterface $order,
        OrderItemInterface $orderItem1,
        OrderItemInterface $orderItem2,
        ProductVariantInterface $productVariant1,
        ProductVariantInterface $productVariant2
    ) {
        $order->getItems()->willReturn([$orderItem1, $orderItem2]);

        $orderItem1->getQuantity()->willReturn(5);
        $orderItem2->getQuantity()->willReturn(10);
        $orderItem1->getVariant()->willReturn($productVariant1);
        $orderItem2->getVariant()->willReturn($productVariant2);

        $productVariant1->isTracked()->willReturn(true);
        $productVariant2->isTracked()->willReturn(true);

        $productVariant1->getOnHand()->willReturn(10);
        $productVariant2->getOnHand()->willReturn(10);

        $productVariant1->getName()->willReturn('Variant 1');
        $productVariant2->getName()->willReturn('Variant 2');

        $productVariant1->setOnHand(5)->shouldBeCalled();
        $productVariant2->setOnHand(0)->shouldBeCalled();

        $this->decrease($order);
    }

    function it_increase_quantity_of_product_variant_available_on_hand(
        OrderInterface $order,
        OrderItemInterface $orderItem1,
        OrderItemInterface $orderItem2,
        ProductVariantInterface $productVariant1,
        ProductVariantInterface $productVariant2
    ) {
        $order->getItems()->willReturn([$orderItem1, $orderItem2]);

        $orderItem1->getQuantity()->willReturn(5);
        $orderItem2->getQuantity()->willReturn(10);
        $orderItem1->getVariant()->willReturn($productVariant1);
        $orderItem2->getVariant()->willReturn($productVariant2);

        $productVariant1->isTracked()->willReturn(true);
        $productVariant2->isTracked()->willReturn(true);

        $productVariant1->getOnHand()->willReturn(10);
        $productVariant2->getOnHand()->willReturn(10);

        $productVariant1->getName()->willReturn('Variant 1');
        $productVariant2->getName()->willReturn('Variant 2');

        $productVariant1->setOnHand(15)->shouldBeCalled();
        $productVariant2->setOnHand(20)->shouldBeCalled();

        $this->increase($order);
    }

    function it_throws_exception_when_recalculated_on_hand_quantity_is_lower_than_zero(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant
    ) {
        $order->getItems()->willReturn([$orderItem]);

        $orderItem->getQuantity()->willReturn(5);
        $orderItem->getVariant()->willReturn($productVariant);
        $productVariant->isTracked()->willReturn(true);
        $productVariant->getOnHand()->willReturn(4);
        $productVariant->getName()->willReturn('Variant');

        $this->shouldThrow(\InvalidArgumentException::class)->during('decrease', [$order]);
    }
}
