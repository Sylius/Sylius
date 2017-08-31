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
use Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\OrderProcessing\OrderPricesRecalculator;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class OrderPricesRecalculatorSpec extends ObjectBehavior
{
    function let(ProductVariantPriceCalculatorInterface $productVariantPriceCalculator)
    {
        $this->beConstructedWith($productVariantPriceCalculator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderPricesRecalculator::class);
    }

    function it_is_an_order_processor()
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
        ProductVariantPriceCalculatorInterface $productVariantPriceCalculator
    ) {
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
        $item->setUnitPrice(10)->shouldBeCalled();

        $this->process($order);
    }

    function it_throws_exception_if_passed_order_is_not_a_core_order(BaseOrderInterface $order)
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('process', [$order])
        ;
    }
}
