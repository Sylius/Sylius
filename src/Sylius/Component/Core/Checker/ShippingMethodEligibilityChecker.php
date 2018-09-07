<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Checker;

use Sylius\Component\Addressing\Provider\ZoneCountriesProviderInterface;
use Sylius\Component\Channel\Resolver\ShippableCountriesResolverInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Shipping\Checker\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;
use Webmozart\Assert\Assert;

final class ShippingMethodEligibilityChecker implements ShippingMethodEligibilityCheckerInterface
{
    /** @var ShippingMethodEligibilityCheckerInterface */
    private $baseShippingMethodEligibilityChecker;

    /** @var ShippableCountriesResolverInterface */
    private $shippableCountriesResolver;

    /** @var ZoneCountriesProviderInterface */
    private $zoneCountriesProvider;

    public function __construct(
        ShippingMethodEligibilityCheckerInterface $baseShippingMethodEligibilityChecker,
        ShippableCountriesResolverInterface $shippableCountriesResolver,
        ZoneCountriesProviderInterface $zoneCountriesProvider
    ) {
        $this->baseShippingMethodEligibilityChecker = $baseShippingMethodEligibilityChecker;
        $this->shippableCountriesResolver = $shippableCountriesResolver;
        $this->zoneCountriesProvider = $zoneCountriesProvider;
    }

    public function isEligible(ShippingSubjectInterface $subject, ShippingMethodInterface $method): bool
    {
        Assert::isInstanceOf($subject, ShipmentInterface::class);

        if (!$this->baseShippingMethodEligibilityChecker->isEligible($subject, $method)) {
            return false;
        }

        $channel = $subject->getOrder()->getChannel();

        $shippableCountries = ($this->shippableCountriesResolver)($channel);
        $zoneCountries = $this->zoneCountriesProvider->getCountriesInWhichZoneOperates($method->getZone());

        foreach ($zoneCountries as $zoneCountry) {
            if (!in_array($zoneCountry, $shippableCountries)) {
                return false;
            }
        }

        return true;
    }
}
