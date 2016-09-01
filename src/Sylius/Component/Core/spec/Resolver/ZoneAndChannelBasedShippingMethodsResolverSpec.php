<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
use Sylius\Component\Core\Resolver\ZoneAndChannelBasedShippingMethodsResolver;
use Sylius\Component\Shipping\Checker\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;

/**
 * @mixin ZoneAndChannelBasedShippingMethodsResolver
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ZoneAndChannelBasedShippingMethodsResolverSpec extends ObjectBehavior
{
    function let(
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ZoneMatcherInterface $zoneMatcher,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker
    ) {
        $this->beConstructedWith($shippingMethodRepository, $zoneMatcher, $eligibilityChecker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Resolver\ZoneAndChannelBasedShippingMethodsResolver');
    }

    function it_implements_shipping_methods_by_zones_and_channel_resolver_interface()
    {
        $this->shouldImplement(ShippingMethodsResolverInterface::class);
    }

    function it_returns_shipping_methods_matched_for_shipment_order_shipping_address_and_order_channel(
        $eligibilityChecker,
        AddressInterface $address,
        ChannelInterface $channel,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $firstShippingMethod,
        ShippingMethodInterface $secondShippingMethod,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ZoneInterface $firstZone,
        ZoneInterface $secondZone,
        ZoneMatcherInterface $zoneMatcher
    ) {
        $shipment->getOrder()->willReturn($order);
        $order->getShippingAddress()->willReturn($address);
        $order->getChannel()->willReturn($channel);

        $zoneMatcher->matchAll($address)->willReturn([$firstZone, $secondZone]);

        $eligibilityChecker->isEligible(Argument::any(), Argument::any())->willReturn(true);

        $shippingMethodRepository
            ->findEnabledForZonesAndChannel([$firstZone, $secondZone], $channel)
            ->willReturn([$firstShippingMethod, $secondShippingMethod])
        ;

        $this->getSupportedMethods($shipment)->shouldReturn([$firstShippingMethod, $secondShippingMethod]);
    }

    function it_returns_empty_array_if_zone_matcher_could_not_match_any_zone(
        $eligibilityChecker,
        OrderInterface $order,
        AddressInterface $address,
        ChannelInterface $channel,
        ShipmentInterface $shipment,
        ZoneMatcherInterface $zoneMatcher
    ) {
        $shipment->getOrder()->willReturn($order);
        $order->getShippingAddress()->willReturn($address);
        $order->getChannel()->willReturn($channel);

        $zoneMatcher->matchAll($address)->willReturn([]);

        $eligibilityChecker->isEligible(Argument::any(), Argument::any())->willReturn(true);

        $this->getSupportedMethods($shipment)->shouldReturn([]);
    }

    function it_returns_only_shipping_methods_that_are_eligible(
        $eligibilityChecker,
        AddressInterface $address,
        ChannelInterface $channel,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $firstShippingMethod,
        ShippingMethodInterface $secondShippingMethod,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ZoneInterface $firstZone,
        ZoneInterface $secondZone,
        ZoneMatcherInterface $zoneMatcher
    ) {
        $shipment->getOrder()->willReturn($order);
        $order->getShippingAddress()->willReturn($address);
        $order->getChannel()->willReturn($channel);

        $zoneMatcher->matchAll($address)->willReturn([$firstZone, $secondZone]);

        $eligibilityChecker->isEligible(Argument::any(), $firstShippingMethod)->willReturn(false);
        $eligibilityChecker->isEligible(Argument::any(), $secondShippingMethod)->willReturn(true);

        $shippingMethodRepository
            ->findEnabledForZonesAndChannel([$firstZone, $secondZone], $channel)
            ->willReturn([$firstShippingMethod, $secondShippingMethod])
        ;

        $this->getSupportedMethods($shipment)->shouldReturn([$secondShippingMethod]);
    }

    function it_supports_shipments_with_order_and_its_shipping_address_defined(
        OrderInterface $order,
        AddressInterface $address,
        ChannelInterface $channel,
        ShipmentInterface $shipment
    ) {
        $shipment->getOrder()->willReturn($order);
        $order->getShippingAddress()->willReturn($address);
        $order->getChannel()->willReturn($channel);

        $this->supports($shipment)->shouldReturn(true);
    }

    function it_does_not_support_shipments_which_order_has_no_shipping_address_defined(
        OrderInterface $order,
        ChannelInterface $channel,
        ShipmentInterface $shipment
    ) {
        $shipment->getOrder()->willReturn($order);
        $order->getShippingAddress()->willReturn(null);
        $order->getChannel()->willReturn($channel);

        $this->supports($shipment)->shouldReturn(false);
    }

    function it_does_not_support_shipments_for_order_with_not_assigned_channel(
        OrderInterface $order,
        AddressInterface $address,
        ShipmentInterface $shipment
    ) {
        $shipment->getOrder()->willReturn($order);
        $order->getShippingAddress()->willReturn($address);
        $order->getChannel()->willReturn(null);

        $this->supports($shipment)->shouldReturn(false);
    }

    function it_does_not_support_shipments_which_has_no_order_defined(ShipmentInterface $shipment)
    {
        $shipment->getOrder()->willReturn(null);

        $this->supports($shipment)->shouldReturn(false);
    }

    function it_does_not_support_different_shipping_subject_than_shipment(ShippingSubjectInterface $subject)
    {
        $this->supports($subject)->shouldReturn(false);
    }
}
