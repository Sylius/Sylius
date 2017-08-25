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

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\Scope;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Core\OrderProcessing\OrderTaxesProcessor;
use Sylius\Component\Core\Provider\ZoneProviderInterface;
use Sylius\Component\Core\Taxation\Exception\UnsupportedTaxCalculationStrategyException;
use Sylius\Component\Core\Taxation\Strategy\TaxCalculationStrategyInterface;
use Sylius\Component\Registry\PrioritizedServiceRegistryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
final class OrderTaxesProcessorSpec extends ObjectBehavior
{
    function let(
        ZoneProviderInterface $defaultTaxZoneProvider,
        ZoneMatcherInterface $zoneMatcher,
        PrioritizedServiceRegistryInterface $strategyRegistry
    ) {
        $this->beConstructedWith($defaultTaxZoneProvider, $zoneMatcher, $strategyRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderTaxesProcessor::class);
    }

    function it_is_an_order_processor()
    {
        $this->shouldImplement(OrderProcessorInterface::class);
    }

    function it_processes_taxes_using_a_supported_tax_calculation_strategy(
        ZoneMatcherInterface $zoneMatcher,
        PrioritizedServiceRegistryInterface $strategyRegistry,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        AddressInterface $address,
        ZoneInterface $zone,
        TaxCalculationStrategyInterface $strategyOne,
        TaxCalculationStrategyInterface $strategyTwo
    ) {
        $order->getItems()->willReturn([$orderItem]);
        $order->isEmpty()->willReturn(false);
        $order->getShippingAddress()->willReturn($address);

        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $orderItem->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $strategyRegistry->all()->willReturn([$strategyOne, $strategyTwo]);
        $zoneMatcher->match($address, Scope::TAX)->willReturn($zone);

        $strategyOne->supports($order, $zone)->willReturn(false);
        $strategyOne->applyTaxes($order, $zone)->shouldNotBeCalled();

        $strategyTwo->supports($order, $zone)->willReturn(true);
        $strategyTwo->applyTaxes($order, $zone)->shouldBeCalled();

        $this->process($order);
    }

    function it_throws_an_exception_if_there_are_no_supported_tax_calculation_strategies(
        ZoneMatcherInterface $zoneMatcher,
        PrioritizedServiceRegistryInterface $strategyRegistry,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        AddressInterface $address,
        ZoneInterface $zone,
        TaxCalculationStrategyInterface $strategy
    ) {
        $order->getItems()->willReturn([$orderItem]);
        $order->isEmpty()->willReturn(false);
        $order->getShippingAddress()->willReturn($address);

        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $orderItem->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $zoneMatcher->match($address, Scope::TAX)->willReturn($zone);

        $strategyRegistry->all()->willReturn([$strategy]);

        $strategy->supports($order, $zone)->willReturn(false);
        $strategy->applyTaxes($order, $zone)->shouldNotBeCalled();

        $this->shouldThrow(UnsupportedTaxCalculationStrategyException::class)->during('process', [$order]);
    }

    function it_does_not_process_taxes_if_there_is_no_order_item(OrderInterface $order)
    {
        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $order->getItems()->willReturn([]);
        $order->isEmpty()->willReturn(true);

        $order->getShippingAddress()->shouldNotBeCalled();

        $this->process($order);
    }

    function it_does_not_process_taxes_if_there_is_no_tax_zone(
        ZoneProviderInterface $defaultTaxZoneProvider,
        ZoneMatcherInterface $zoneMatcher,
        PrioritizedServiceRegistryInterface $strategyRegistry,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        AddressInterface $address
    ) {
        $order->getItems()->willReturn([$orderItem]);
        $order->isEmpty()->willReturn(false);

        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $orderItem->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $order->getShippingAddress()->willReturn($address);

        $zoneMatcher->match($address, Scope::TAX)->willReturn(null);

        $defaultTaxZoneProvider->getZone($order)->willReturn(null);

        $strategyRegistry->all()->shouldNotBeCalled();

        $this->process($order);
    }
}
