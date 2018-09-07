<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Provider\ZoneCountriesProviderInterface;
use Sylius\Component\Channel\Resolver\ShippableCountriesResolverInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Checker\ShippingMethodEligibilityCheckerInterface;

final class ShippingMethodEligibilityCheckerSpec extends ObjectBehavior
{
    function let(
        ShippingMethodEligibilityCheckerInterface $baseShippingMethodEligibilityChecker,
        ShippableCountriesResolverInterface $shippableCountriesResolver,
        ZoneCountriesProviderInterface $zoneCountriesProvider
    ): void {
        $this->beConstructedWith(
            $baseShippingMethodEligibilityChecker,
            $shippableCountriesResolver,
            $zoneCountriesProvider
        );
    }

    function it_implements_shipping_method_eligibility_checker_interface(): void
    {
        $this->shouldImplement(ShippingMethodEligibilityCheckerInterface::class);
    }

    function it_returns_false_if_base_checker_says_so(
        ShippingMethodEligibilityCheckerInterface $baseShippingMethodEligibilityChecker,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod
    ): void {
        $baseShippingMethodEligibilityChecker->isEligible($shipment, $shippingMethod)->willReturn(false);

        $this->isEligible($shipment, $shippingMethod)->shouldReturn(false);
    }

    function it_returns_false_if_zone_counties_are_not_shippable_within_channel(
        ShippingMethodEligibilityCheckerInterface $baseShippingMethodEligibilityChecker,
        ShippableCountriesResolverInterface $shippableCountriesResolver,
        ZoneCountriesProviderInterface $zoneCountriesProvider,
        ShipmentInterface $shipment,
        OrderInterface $order,
        ChannelInterface $channel,
        ShippingMethodInterface $shippingMethod,
        ZoneInterface $zone,
        CountryInterface $firstCountry,
        CountryInterface $secondCountry,
        CountryInterface $thirdCountry
    ): void {
        $baseShippingMethodEligibilityChecker->isEligible($shipment, $shippingMethod)->willReturn(true);

        $shipment->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);

        $shippingMethod->getZone()->willReturn($zone);

        $shippableCountriesResolver->__invoke($channel)->willReturn([$firstCountry, $secondCountry]);
        $zoneCountriesProvider->getCountriesInWhichZoneOperates($zone)->willReturn([$thirdCountry]);

        $this->isEligible($shipment, $shippingMethod)->shouldReturn(false);
    }

    function it_returns_true_if_zone_counties_are_shippable_within_channel(
        ShippingMethodEligibilityCheckerInterface $baseShippingMethodEligibilityChecker,
        ShippableCountriesResolverInterface $shippableCountriesResolver,
        ZoneCountriesProviderInterface $zoneCountriesProvider,
        ShipmentInterface $shipment,
        OrderInterface $order,
        ChannelInterface $channel,
        ShippingMethodInterface $shippingMethod,
        ZoneInterface $zone,
        CountryInterface $firstCountry,
        CountryInterface $secondCountry
    ): void {
        $baseShippingMethodEligibilityChecker->isEligible($shipment, $shippingMethod)->willReturn(true);

        $shipment->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);

        $shippingMethod->getZone()->willReturn($zone);

        $shippableCountriesResolver->__invoke($channel)->willReturn([$firstCountry, $secondCountry]);
        $zoneCountriesProvider->getCountriesInWhichZoneOperates($zone)->willReturn([$firstCountry]);

        $this->isEligible($shipment, $shippingMethod)->shouldReturn(true);
    }
}
