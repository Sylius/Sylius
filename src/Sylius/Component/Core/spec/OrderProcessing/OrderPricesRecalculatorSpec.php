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

namespace spec\Sylius\Component\Core\OrderProcessing;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderPricesRecalculatorSpec extends ObjectBehavior
{
    function let(ProductVariantPricesCalculatorInterface $productVariantPriceCalculator): void
    {
        $this->beConstructedWith($productVariantPriceCalculator);
    }

    function it_is_an_order_processor(): void
    {
        $this->shouldImplement(OrderProcessorInterface::class);
    }

    function it_recalculates_prices_adding_customer_to_the_context(
        ChannelInterface $channel,
        CustomerGroupInterface $group,
        CustomerInterface $customer,
        OrderInterface $order,
        OrderItemInterface $item,
        ProductVariantInterface $variant,
        ProductVariantPricesCalculatorInterface $productVariantPriceCalculator,
    ): void {
        $order->getState()->willReturn(OrderInterface::STATE_CART);

        $order->getCustomer()->willReturn($customer);
        $order->getChannel()->willReturn(null);
        $order->getItems()->willReturn(new ArrayCollection([$item->getWrappedObject()]));
        $order->getCurrencyCode()->willReturn(null);

        $customer->getGroup()->willReturn($group);

        $item->isImmutable()->willReturn(false);
        $item->getQuantity()->willReturn(5);
        $item->getVariant()->willReturn($variant);

        $order->getChannel()->willReturn($channel);

        $productVariantPriceCalculator
            ->calculate($variant, ['channel' => $channel])
            ->willReturn(10)
        ;
        $productVariantPriceCalculator
            ->calculateOriginal($variant, ['channel' => $channel])
            ->willReturn(20)
        ;
        $item->setUnitPrice(10)->shouldBeCalled();
        $item->setOriginalUnitPrice(20)->shouldBeCalled();

        $this->process($order);
    }

    function it_throws_exception_if_passed_order_is_not_a_core_order(BaseOrderInterface $order): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('process', [$order])
        ;
    }

    function it_does_nothing_if_the_order_is_in_a_state_different_than_cart(OrderInterface $order): void
    {
        $order->getState()->willReturn(OrderInterface::STATE_NEW);

        $order->getChannel()->shouldNotBeCalled();
        $order->getItems()->shouldNotBeCalled();

        $this->process($order);
    }
}
