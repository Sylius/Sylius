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

namespace spec\Sylius\Bundle\CoreBundle\Doctrine\ORM\Inventory\Operator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class OrderInventoryOperatorSpec extends ObjectBehavior
{
    function let(OrderInventoryOperatorInterface $decoratedOperator, EntityManagerInterface $productVariantManager): void
    {
        $this->beConstructedWith($decoratedOperator, $productVariantManager);
    }

    function it_implements_an_order_inventory_operator_interface(): void
    {
        $this->shouldImplement(OrderInventoryOperatorInterface::class);
    }

    function it_locks_tracked_variants_during_cancelling(
        OrderInventoryOperatorInterface $decoratedOperator,
        EntityManagerInterface $productVariantManager,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant
    ): void {
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $orderItem->getVariant()->willReturn($variant);
        $variant->isTracked()->willReturn(true);
        $variant->getVersion()->willReturn('7');

        $productVariantManager->lock($variant, LockMode::OPTIMISTIC, '7')->shouldBeCalled();

        $decoratedOperator->cancel($order)->shouldBeCalled();

        $this->cancel($order);
    }

    function it_locks_tracked_variants_during_holding(
        OrderInventoryOperatorInterface $decoratedOperator,
        EntityManagerInterface $productVariantManager,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant
    ): void {
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $orderItem->getVariant()->willReturn($variant);
        $variant->isTracked()->willReturn(true);
        $variant->getVersion()->willReturn('7');

        $productVariantManager->lock($variant, LockMode::OPTIMISTIC, '7')->shouldBeCalled();

        $decoratedOperator->hold($order)->shouldBeCalled();

        $this->hold($order);
    }

    function it_locks_tracked_variants_during_selling(
        OrderInventoryOperatorInterface $decoratedOperator,
        EntityManagerInterface $productVariantManager,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant
    ): void {
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $orderItem->getVariant()->willReturn($variant);
        $variant->isTracked()->willReturn(true);
        $variant->getVersion()->willReturn('7');

        $productVariantManager->lock($variant, LockMode::OPTIMISTIC, '7')->shouldBeCalled();

        $decoratedOperator->sell($order)->shouldBeCalled();

        $this->sell($order);
    }
}
