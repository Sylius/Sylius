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
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Checker\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Model\ShipmentInterface as BaseShipmentInterface;
use Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolverInterface;

final class EligibleDefaultShippingMethodResolverSpec extends ObjectBehavior
{
    function let(
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodEligibilityCheckerInterface $shippingMethodEligibilityChecker,
        ZoneMatcherInterface $zoneMatcher
    ): void {
        $this->beConstructedWith(
            $shippingMethodRepository,
            $shippingMethodEligibilityChecker,
            $zoneMatcher
        );
    }

    function it_implements_default_shipping_method_resolver_interface()
    {
        $this->shouldImplement(DefaultShippingMethodResolverInterface::class);
    }

    function it_returns_first_enabled_and_eligible_shipping_method_from_shipment_order_channel_as_default(
        ChannelInterface $channel,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $firstShippingMethod,
        ShippingMethodInterface $secondShippingMethod,
        ShippingMethodInterface $thirdShippingMethod,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodEligibilityCheckerInterface $shippingMethodEligibilityChecker
    ): void {
        $shipment->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);
        $order->getShippingAddress()->willReturn(null);

        $shippingMethodRepository
            ->findEnabledForChannel($channel)
            ->willReturn([$firstShippingMethod, $secondShippingMethod])
        ;

        $shippingMethodEligibilityChecker->isEligible($shipment, $firstShippingMethod)->willReturn(false);
        $shippingMethodEligibilityChecker->isEligible($shipment, $secondShippingMethod)->willReturn(true);
        $shippingMethodEligibilityChecker->isEligible($shipment, $thirdShippingMethod)->willReturn(true);

        $this->getDefaultShippingMethod($shipment)->shouldReturn($secondShippingMethod);
    }

    function it_returns_enabled_and_eligible_shipping_method_from_shipment_order_channel_and_shipping_zone_as_default(
        ChannelInterface $channel,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $firstShippingMethod,
        ShippingMethodInterface $secondShippingMethod,
        ShippingMethodInterface $thirdShippingMethod,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodEligibilityCheckerInterface $shippingMethodEligibilityChecker,
        AddressInterface $shippingAddress,
        ZoneMatcherInterface $zoneMatcher,
        ZoneInterface $zone
    ): void {
        $shipment->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);
        $order->getShippingAddress()->willReturn($shippingAddress);

        $zoneMatcher->matchAll($shippingAddress)->willReturn([$zone]);

        $shippingMethodRepository
            ->findEnabledForZonesAndChannel([$zone], $channel)
            ->willReturn([$firstShippingMethod, $secondShippingMethod])
        ;

        $shippingMethodEligibilityChecker->isEligible($shipment, $firstShippingMethod)->willReturn(false);
        $shippingMethodEligibilityChecker->isEligible($shipment, $secondShippingMethod)->willReturn(true);
        $shippingMethodEligibilityChecker->isEligible($shipment, $thirdShippingMethod)->willReturn(true);

        $this->getDefaultShippingMethod($shipment)->shouldReturn($secondShippingMethod);
    }

    function it_throws_an_exception_if_there_is_shipping_method_cannot_be_resolved(
        ChannelInterface $channel,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $firstShippingMethod,
        ShippingMethodInterface $secondShippingMethod,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodEligibilityCheckerInterface $shippingMethodEligibilityChecker
    ): void {
        $shipment->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);
        $order->getShippingAddress()->willReturn(null);

        $shippingMethodRepository
            ->findEnabledForChannel($channel)
            ->willReturn([$firstShippingMethod, $secondShippingMethod])
        ;

        $shippingMethodEligibilityChecker->isEligible($shipment, $firstShippingMethod)->willReturn(false);
        $shippingMethodEligibilityChecker->isEligible($shipment, $secondShippingMethod)->willReturn(false);

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
