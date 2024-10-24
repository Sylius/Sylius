<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\Scope;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;

final class ZoneAndChannelBasedShippingMethodsResolverSpec extends ObjectBehavior
{
    function let(
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ZoneMatcherInterface $zoneMatcher,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
    ): void {
        $this->beConstructedWith($shippingMethodRepository, $zoneMatcher, $eligibilityChecker);
    }

    function it_implements_a_shipping_methods_by_zones_and_channel_resolver_interface(): void
    {
        $this->shouldImplement(ShippingMethodsResolverInterface::class);
    }

    function it_returns_shipping_methods_matched_for_shipment_order_shipping_address_and_order_channel(
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        AddressInterface $address,
        ChannelInterface $channel,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $firstShippingMethod,
        ShippingMethodInterface $secondShippingMethod,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ZoneInterface $firstZone,
        ZoneInterface $secondZone,
        ZoneMatcherInterface $zoneMatcher,
    ): void {
        $shipment->getOrder()->willReturn($order);
        $order->getShippingAddress()->willReturn($address);
        $order->getChannel()->willReturn($channel);

        $zoneMatcher->matchAll($address, Scope::SHIPPING)->willReturn([$firstZone, $secondZone]);

        $shippingMethodRepository
            ->findEnabledForZonesAndChannel([$firstZone, $secondZone], $channel)
            ->willReturn([$firstShippingMethod, $secondShippingMethod])
        ;

        $eligibilityChecker->isEligible($shipment, $firstShippingMethod)->willReturn(true);
        $eligibilityChecker->isEligible($shipment, $secondShippingMethod)->willReturn(true);

        $this->getSupportedMethods($shipment)->shouldReturn([$firstShippingMethod, $secondShippingMethod]);
    }

    function it_returns_an_empty_array_if_zone_matcher_could_not_match_any_zone(
        OrderInterface $order,
        AddressInterface $address,
        ChannelInterface $channel,
        ShipmentInterface $shipment,
        ZoneMatcherInterface $zoneMatcher,
    ): void {
        $shipment->getOrder()->willReturn($order);
        $order->getShippingAddress()->willReturn($address);
        $order->getChannel()->willReturn($channel);

        $zoneMatcher->matchAll($address, Scope::SHIPPING)->willReturn([]);

        $this->getSupportedMethods($shipment)->shouldReturn([]);
    }

    function it_returns_only_shipping_methods_that_are_eligible(
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        AddressInterface $address,
        ChannelInterface $channel,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $firstShippingMethod,
        ShippingMethodInterface $secondShippingMethod,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ZoneInterface $firstZone,
        ZoneInterface $secondZone,
        ZoneMatcherInterface $zoneMatcher,
    ): void {
        $shipment->getOrder()->willReturn($order);
        $order->getShippingAddress()->willReturn($address);
        $order->getChannel()->willReturn($channel);

        $zoneMatcher->matchAll($address, Scope::SHIPPING)->willReturn([$firstZone, $secondZone]);

        $eligibilityChecker->isEligible($shipment, $firstShippingMethod)->willReturn(false);
        $eligibilityChecker->isEligible($shipment, $secondShippingMethod)->willReturn(true);

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
        ShipmentInterface $shipment,
    ): void {
        $shipment->getOrder()->willReturn($order);
        $order->getShippingAddress()->willReturn($address);
        $order->getChannel()->willReturn($channel);

        $this->supports($shipment)->shouldReturn(true);
    }

    function it_does_not_support_shipments_which_order_has_no_shipping_address_defined(
        OrderInterface $order,
        ChannelInterface $channel,
        ShipmentInterface $shipment,
    ): void {
        $shipment->getOrder()->willReturn($order);
        $order->getShippingAddress()->willReturn(null);
        $order->getChannel()->willReturn($channel);

        $this->supports($shipment)->shouldReturn(false);
    }

    function it_does_not_support_shipments_for_order_with_not_assigned_channel(
        OrderInterface $order,
        AddressInterface $address,
        ShipmentInterface $shipment,
    ): void {
        $shipment->getOrder()->willReturn($order);
        $order->getShippingAddress()->willReturn($address);
        $order->getChannel()->willReturn(null);

        $this->supports($shipment)->shouldReturn(false);
    }

    function it_does_not_support_shipments_which_has_no_order_defined(ShipmentInterface $shipment): void
    {
        $shipment->getOrder()->willReturn(null);

        $this->supports($shipment)->shouldReturn(false);
    }

    function it_does_not_support_different_shipping_subject_than_shipment(ShippingSubjectInterface $subject): void
    {
        $this->supports($subject)->shouldReturn(false);
    }
}
