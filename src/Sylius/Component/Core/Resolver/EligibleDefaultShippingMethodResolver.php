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
use Sylius\Component\Core\Model\ShipmentInterface as CoreShipmentInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Checker\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolverInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Webmozart\Assert\Assert;

final class EligibleDefaultShippingMethodResolver implements DefaultShippingMethodResolverInterface
{
    /** @var ShippingMethodRepositoryInterface */
    private $shippingMethodRepository;

    /** @var ShippingMethodEligibilityCheckerInterface */
    private $shippingMethodEligibilityChecker;

    /** @var ZoneMatcherInterface */
    private $zoneMatcher;

    /** @var ShippingMethodsResolverInterface|null */
    private $shippingMethodsResolver;

    public function __construct(
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodEligibilityCheckerInterface $shippingMethodEligibilityChecker,
        ZoneMatcherInterface $zoneMatcher,
        ?ShippingMethodsResolverInterface $shippingMethodsResolver = null
    ) {
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->shippingMethodEligibilityChecker = $shippingMethodEligibilityChecker;
        $this->zoneMatcher = $zoneMatcher;

        if (null === $shippingMethodsResolver) {
            @trigger_error(
                sprintf(
                    'Not passing an $shippingMethodsResolver to "%s" constructor is deprecated since Sylius 1.8 and will be impossible in Sylius 2.0.',
                    self::class
                ),
                \E_USER_DEPRECATED
            );
        }

        $this->shippingMethodsResolver = $shippingMethodsResolver;
    }

    /**
     * @param ShipmentInterface|CoreShipmentInterface $shipment
     *
     * @throws UnresolvedDefaultShippingMethodException
     */
    public function getDefaultShippingMethod(ShipmentInterface $shipment): ShippingMethodInterface
    {
        Assert::isInstanceOf($shipment, CoreShipmentInterface::class);

        if (null !== $this->shippingMethodsResolver) {
            return $this->getFromResolver($shipment);
        }

        return $this->getFromRepository($shipment);
    }

    /**
     * @throws UnresolvedDefaultShippingMethodException
     */
    private function getFromResolver(CoreShipmentInterface $shipment): ShippingMethodInterface
    {
        $shippingMethods = $this->shippingMethodsResolver->getSupportedMethods($shipment);

        if (empty($shippingMethods)) {
            throw new UnresolvedDefaultShippingMethodException();
        }

        return $shippingMethods[0];
    }

    /**
     * @deprecated
     *
     * @throws UnresolvedDefaultShippingMethodException
     */
    private function getFromRepository(CoreShipmentInterface $shipment): ShippingMethodInterface
    {
        $order = $shipment->getOrder();
        $shippingMethods = $this->getShippingMethods($order->getChannel(), $order->getShippingAddress());

        foreach ($shippingMethods as $shippingMethod) {
            if ($this->shippingMethodEligibilityChecker->isEligible($shipment, $shippingMethod)) {
                return $shippingMethod;
            }
        }

        throw new UnresolvedDefaultShippingMethodException();
    }

    /**
     * @deprecated
     *
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
