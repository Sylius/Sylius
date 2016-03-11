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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Provider\ZoneProviderInterface;
use Sylius\Component\Core\Taxation\Processor\OrderTaxesProcessorInterface;
use Sylius\Component\Registry\PrioritizedServiceRegistryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
class OrderTaxesProcessorSpec extends ObjectBehavior
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

    function it_applies_taxes_for_order_items_units_and_shipment(
        $zoneMatcher,
        AddressInterface $address,
        \Iterator $iterator,
        Collection $items,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ZoneInterface $zone
    ) {
        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $order->getItems()->willReturn($items);
        $order->isEmpty()->willReturn(false);

        $items->count()->willReturn(1);
        $items->getIterator()->willReturn($iterator, $iterator);
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, false, true, false)->shouldBeCalled();
        $iterator->current()->willReturn($orderItem, $orderItem);
        $iterator->next()->shouldBeCalled();

        $orderItem->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $order->getShippingAddress()->willReturn($address);
        $zoneMatcher->match($address)->willReturn($zone);

        $this->apply($order);
    }

    function it_does_not_apply_taxes_if_there_is_no_order_item(OrderInterface $order)
    {
        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $order->getItems()->willReturn([]);
        $order->isEmpty()->willReturn(true);

        $order->getShippingAddress()->shouldNotBeCalled();

        $this->apply($order);
    }

    function it_does_not_apply_taxes_if_there_is_no_tax_zone(
        $defaultTaxZoneProvider,
        $zoneMatcher,
        AddressInterface $address,
        \Iterator $iterator,
        Collection $items,
        OrderInterface $order,
        OrderItemInterface $orderItem
    ) {
        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $order->getItems()->willReturn($items);
        $order->isEmpty()->willReturn(false);

        $items->count()->willReturn(1);
        $items->getIterator()->willReturn($iterator);
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, false)->shouldBeCalled();
        $iterator->current()->willReturn($orderItem);
        $iterator->next()->shouldBeCalled();

        $orderItem->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();

        $order->getShippingAddress()->willReturn($address);
        $zoneMatcher->match($address)->willReturn(null);
        $defaultTaxZoneProvider->getZone()->willReturn(null);

        $this->apply($order);
    }
}
