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

namespace spec\Sylius\Component\Core\Resolver;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Model\ShipmentInterface as BaseShipmentInterface;
use Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolverInterface;

final class DefaultShippingMethodResolverSpec extends ObjectBehavior
{
    function let(
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ZoneMatcherInterface $zoneMatcher
    ): void {
        $this->beConstructedWith($shippingMethodRepository, $zoneMatcher);
    }

    function it_implements_a_default_shipping_method_resolver_interface(): void
    {
        $this->shouldImplement(DefaultShippingMethodResolverInterface::class);
    }

    function it_returns_first_enabled_shipping_method_from_shipment_order_channel_if_there_is_not_shipping_address(
        ChannelInterface $channel,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $firstShippingMethod,
        ShippingMethodInterface $secondShippingMethod,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ZoneMatcherInterface $zoneMatcher
    ): void {
        $shipment->getOrder()->willReturn($order);

        $order->getChannel()->willReturn($channel);
        $order->getShippingAddress()->willReturn(null);

        $zoneMatcher->matchAll(Argument::any())->shouldNotBeCalled();

        $shippingMethodRepository
            ->findEnabledForChannel($channel)
            ->willReturn([$firstShippingMethod, $secondShippingMethod])
        ;

        $this->getDefaultShippingMethod($shipment)->shouldReturn($firstShippingMethod);
    }

    function it_returns_first_enabled_shipping_method_matched_by_order_channel_and_shipping_address_zone(
        AddressInterface $shippingAddress,
        ChannelInterface $channel,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $firstShippingMethod,
        ShippingMethodInterface $secondShippingMethod,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ZoneInterface $firstZone,
        ZoneInterface $secondZone,
        ZoneMatcherInterface $zoneMatcher
    ): void {
        $shipment->getOrder()->willReturn($order);

        $order->getChannel()->willReturn($channel);
        $order->getShippingAddress()->willReturn($shippingAddress);

        $zoneMatcher->matchAll($shippingAddress)->willReturn([$firstZone, $secondZone]);

        $shippingMethodRepository
            ->findEnabledForZonesAndChannel([$firstZone, $secondZone], $channel)
            ->willReturn([$firstShippingMethod, $secondShippingMethod])
        ;

        $this->getDefaultShippingMethod($shipment)->shouldReturn($firstShippingMethod);
    }

    function it_throws_an_exception_if_there_is_no_enabled_shipping_methods_for_order_channel_and_zones(
        AddressInterface $shippingAddress,
        ChannelInterface $channel,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ZoneInterface $firstZone,
        ZoneInterface $secondZone,
        ZoneMatcherInterface $zoneMatcher
    ): void {
        $shipment->getOrder()->willReturn($order);

        $order->getChannel()->willReturn($channel);
        $order->getShippingAddress()->willReturn($shippingAddress);

        $zoneMatcher->matchAll($shippingAddress)->willReturn([$firstZone, $secondZone]);

        $shippingMethodRepository
            ->findEnabledForZonesAndChannel([$firstZone, $secondZone], $channel)
            ->willReturn([])
        ;

        $this
            ->shouldThrow(UnresolvedDefaultShippingMethodException::class)
            ->during('getDefaultShippingMethod', [$shipment])
        ;
    }

    function it_throws_an_exception_if_passed_shipment_is_not_core_shipment_object(BaseShipmentInterface $shipment): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('getDefaultShippingMethod', [$shipment]);
    }
}
