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
use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolverInterface;
use Webmozart\Assert\Assert;

class DefaultShippingMethodResolver implements DefaultShippingMethodResolverInterface
{
    /**
     * @var ShippingMethodRepositoryInterface
     */
    private $shippingMethodRepository;

    /**
     * @var ZoneMatcherInterface
     */
    private $zoneMatcher;

    /**
     * @param ShippingMethodRepositoryInterface $shippingMethodRepository
     * @param ZoneMatcherInterface $zoneMatcher
     */
    public function __construct(
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ZoneMatcherInterface $zoneMatcher
    ) {
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->zoneMatcher = $zoneMatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultShippingMethod(ShipmentInterface $shipment): ShippingMethodInterface
    {
        /** @var CoreShipmentInterface $shipment */
        Assert::isInstanceOf($shipment, CoreShipmentInterface::class);

        $order = $shipment->getOrder();

        /** @var ChannelInterface $channel */
        $channel = $order->getChannel();
        /** @var AddressInterface $shippingAddress */
        $shippingAddress = $order->getShippingAddress();

        $shippingMethods = $this->getShippingMethods($channel, $shippingAddress);
        if (empty($shippingMethods)) {
            throw new UnresolvedDefaultShippingMethodException();
        }

        return $shippingMethods[0];
    }

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
