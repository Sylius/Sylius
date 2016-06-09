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
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Resolver\ShippingMethodsByZonesAndChannelResolver;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;
use Sylius\Component\Shipping\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Resolver\MethodsResolverInterface;

/**
 * @mixin ShippingMethodsByZonesAndChannelResolver
 * 
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ShippingMethodsByZonesAndChannelResolverSpec extends ObjectBehavior
{
    function let(
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ZoneMatcherInterface $zoneMatcher
    ) {
        $this->beConstructedWith($shippingMethodRepository, $zoneMatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Resolver\ShippingMethodsByZonesAndChannelResolver');
    }

    function it_implements_shipping_methods_by_zones_and_channel_resolver_interface()
    {
        $this->shouldImplement(MethodsResolverInterface::class);
    }

    function it_returns_shipping_methods_matched_for_shipment_order_shipping_address_and_order_channel(
        AddressInterface $address,
        ChannelInterface $channel,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $firstShippingMethod,
        ShippingMethodInterface $secondShippingMethod,
        ShippingMethodInterface $thirdShippingMethod,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ZoneInterface $firstZone,
        ZoneInterface $secondZone,
        ZoneMatcherInterface $zoneMatcher
    ) {
        $shipment->getOrder()->willReturn($order);
        $order->getShippingAddress()->willReturn($address);
        $order->getChannel()->willReturn($channel);

        $zoneMatcher->matchAll($address)->willReturn([$firstZone, $secondZone]);

        $firstZone->getId()->willReturn(1);
        $secondZone->getId()->willReturn(4);

        $shippingMethodRepository
            ->findBy(['enabled' => true, 'zone' => [1, 4]])
            ->willReturn([$firstShippingMethod, $secondShippingMethod, $thirdShippingMethod])
        ;

        $channel->hasShippingMethod($firstShippingMethod)->willReturn(true);
        $channel->hasShippingMethod($secondShippingMethod)->willReturn(true);
        $channel->hasShippingMethod($thirdShippingMethod)->willReturn(false);

        $this->getSupportedMethods($shipment)->shouldReturn([$firstShippingMethod, $secondShippingMethod]);
    }

    function it_returns_empty_array_if_zone_matcher_could_not_match_any_zone(
        AddressInterface $address,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ZoneMatcherInterface $zoneMatcher
    ) {
        $shipment->getOrder()->willReturn($order);
        $order->getShippingAddress()->willReturn($address);

        $zoneMatcher->matchAll($address)->willReturn([]);

        $this->getSupportedMethods($shipment)->shouldReturn([]);
    }

    function it_supports_shipments_with_order_and_its_shipping_address_defined(
        AddressInterface $address,
        OrderInterface $order,
        ShipmentInterface $shipment
    ) {
        $shipment->getOrder()->willReturn($order);
        $order->getShippingAddress()->willReturn($address);

        $this->supports($shipment)->shouldReturn(true);
    }

    function it_does_not_support_shipments_which_order_has_no_shipping_address_defined(
        OrderInterface $order,
        ShipmentInterface $shipment
    ) {
        $shipment->getOrder()->willReturn($order);
        $order->getShippingAddress()->willReturn(null);

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
