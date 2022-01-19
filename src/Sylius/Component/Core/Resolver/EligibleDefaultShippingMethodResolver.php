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

namespace Sylius\Component\Core\Resolver;

use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface as CoreShipmentInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolverInterface;
use Webmozart\Assert\Assert;

final class EligibleDefaultShippingMethodResolver implements DefaultShippingMethodResolverInterface
{
    public function __construct(
        private ShippingMethodRepositoryInterface $shippingMethodRepository,
        private ShippingMethodEligibilityCheckerInterface $shippingMethodEligibilityChecker,
        private ZoneMatcherInterface $zoneMatcher
    ) {
    }

    public function getDefaultShippingMethod(ShipmentInterface $shipment): ShippingMethodInterface
    {
        /** @var CoreShipmentInterface $shipment */
        Assert::isInstanceOf($shipment, CoreShipmentInterface::class);

        /** @var OrderInterface $order */
        $order = $shipment->getOrder();
        /** @var ChannelInterface $channel */
        $channel = $order->getChannel();

        $shippingMethods = $this->getShippingMethods($channel, $order->getShippingAddress());

        foreach ($shippingMethods as $shippingMethod) {
            if ($this->shippingMethodEligibilityChecker->isEligible($shipment, $shippingMethod)) {
                return $shippingMethod;
            }
        }

        throw new UnresolvedDefaultShippingMethodException();
    }

    /**
     * @return array|ShippingMethodInterface[]
     */
    private function getShippingMethods(ChannelInterface $channel, ?AddressInterface $address): array
    {
        if (null === $address) {
            return $this->shippingMethodRepository->findEnabledForChannel($channel);
        }

        /** @var ZoneInterface[] $zones */
        $zones = $this->zoneMatcher->matchAll($address);

        return $this->shippingMethodRepository->findEnabledForZonesAndChannel($zones, $channel);
    }
}
