<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Taxation\Processor;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Provider\ZoneProviderInterface;
use Sylius\Component\Core\Taxation\Exception\UnsupportedTaxCalculationStrategyException;
use Sylius\Component\Core\Taxation\Processor\OrderTaxesProcessor;
use Sylius\Component\Core\Taxation\Processor\OrderTaxesProcessorInterface;
use Sylius\Component\Core\Taxation\Strategy\TaxCalculationStrategyInterface;
use Sylius\Component\Registry\PrioritizedServiceRegistryInterface;
use Zend\Stdlib\PriorityQueue;

/**
 * @mixin OrderTaxesProcessor
 *
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
        $this->shouldHaveType('Sylius\Component\Core\Taxation\Processor\OrderTaxesProcessor');
    }

    function it_implements_Sylius_taxation_processor_interface()
    {
        $this->shouldImplement(OrderTaxesProcessorInterface::class);
    }

    function it_processes_taxes_using_a_supported_tax_calculation_strategy(
        ZoneMatcherInterface $zoneMatcher,
        AddressInterface $address,
        \Iterator $itemsIterator,
        Collection $items,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ZoneInterface $zone,
        PrioritizedServiceRegistryInterface $strategyRegistry,
        PriorityQueue $strategies,
        \Iterator $strategiesIterator,
        TaxCalculationStrategyInterface $strategyOne,
        TaxCalculationStrategyInterface $strategyTwo
    ) {
        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $order->getItems()->willReturn($items);
        $order->isEmpty()->willReturn(false);

        $items->count()->willReturn(1);
        $items->getIterator()->willReturn($itemsIterator);
        $itemsIterator->rewind()->shouldBeCalled();
        $itemsIterator->valid()->willReturn(true, false)->shouldBeCalled();
        $itemsIterator->current()->willReturn($orderItem);
        $itemsIterator->next()->shouldBeCalled();

        $orderItem->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $order->getShippingAddress()->willReturn($address);
        $zoneMatcher->match($address)->willReturn($zone);

        $strategyRegistry->all()->willReturn($strategies);

        $strategies->count()->willReturn(2);
        $strategies->getIterator()->willReturn($strategiesIterator);
        $strategiesIterator->rewind()->shouldBeCalled();
        $strategiesIterator->valid()->willReturn(true, true, false)->shouldBeCalled();
        $strategiesIterator->current()->willReturn($strategyOne, $strategyTwo);
        $strategiesIterator->next()->shouldBeCalled();

        $strategyOne->supports($order, $zone)->willReturn(false)->shouldBeCalled();
        $strategyOne->applyTaxes($order, $zone)->shouldNotBeCalled();

        $strategyTwo->supports($order, $zone)->willReturn(true)->shouldBeCalled();
        $strategyTwo->applyTaxes($order, $zone)->shouldBeCalled();

        $this->shouldNotThrow(new UnsupportedTaxCalculationStrategyException())->duringProcess($order);
    }

    function it_throws_an_exception_if_there_are_no_supported_tax_calculation_strategies(
        ZoneMatcherInterface $zoneMatcher,
        AddressInterface $address,
        \Iterator $itemsIterator,
        Collection $items,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ZoneInterface $zone,
        PrioritizedServiceRegistryInterface $strategyRegistry,
        PriorityQueue $strategies,
        \Iterator $strategiesIterator,
        TaxCalculationStrategyInterface $strategy
    ) {
        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $order->getItems()->willReturn($items);
        $order->isEmpty()->willReturn(false);

        $items->count()->willReturn(1);
        $items->getIterator()->willReturn($itemsIterator);
        $itemsIterator->rewind()->shouldBeCalled();
        $itemsIterator->valid()->willReturn(true, false)->shouldBeCalled();
        $itemsIterator->current()->willReturn($orderItem);
        $itemsIterator->next()->shouldBeCalled();

        $orderItem->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $order->getShippingAddress()->willReturn($address);
        $zoneMatcher->match($address)->willReturn($zone);

        $strategyRegistry->all()->willReturn($strategies);

        $strategies->count()->willReturn(1);
        $strategies->getIterator()->willReturn($strategiesIterator);
        $strategiesIterator->rewind()->shouldBeCalled();
        $strategiesIterator->valid()->willReturn(true, false)->shouldBeCalled();
        $strategiesIterator->current()->willReturn($strategy);
        $strategiesIterator->next()->shouldBeCalled();

        $strategy->supports($order, $zone)->willReturn(false)->shouldBeCalled();
        $strategy->applyTaxes($order, $zone)->shouldNotBeCalled();

        $this->shouldThrow(new UnsupportedTaxCalculationStrategyException())->duringProcess($order);
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
        AddressInterface $address,
        \Iterator $itemsIterator,
        Collection $items,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        PrioritizedServiceRegistryInterface $strategyRegistry
    ) {
        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $order->getItems()->willReturn($items);
        $order->isEmpty()->willReturn(false);

        $items->count()->willReturn(1);
        $items->getIterator()->willReturn($itemsIterator);
        $itemsIterator->rewind()->shouldBeCalled();
        $itemsIterator->valid()->willReturn(true, false)->shouldBeCalled();
        $itemsIterator->current()->willReturn($orderItem);
        $itemsIterator->next()->shouldBeCalled();

        $orderItem->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $order->getShippingAddress()->willReturn($address);
        $zoneMatcher->match($address)->willReturn(null);
        $defaultTaxZoneProvider->getZone($order)->willReturn(null);

        $strategyRegistry->all()->shouldNotBeCalled();

        $this->process($order);
    }
}
