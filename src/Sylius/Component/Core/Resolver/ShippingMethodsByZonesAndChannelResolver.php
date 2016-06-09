<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Resolver;

use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;
use Sylius\Component\Shipping\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Resolver\MethodsResolverInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ShippingMethodsByZonesAndChannelResolver implements MethodsResolverInterface
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
    public function getSupportedMethods(ShippingSubjectInterface $subject)
    {
        $zones = $this->getZonesIdsForAddress($subject->getOrder());
        if (empty($zones)) {
            return [];
        }

        /** @var ChannelInterface $channel */
        $channel = $subject->getOrder()->getChannel();

        $methods = [];
        foreach ($this->shippingMethodRepository->findBy(['enabled' => true, 'zone' => $zones]) as $method) {
            if ($channel->hasShippingMethod($method)) {
                $methods[] = $method;
            }
        }

        return $methods;
    }

    /**
     * @param OrderInterface $order
     *
     * @return array
     */
    private function getZonesIdsForAddress(OrderInterface $order)
    {
        $matchedZones = $this->zoneMatcher->matchAll($order->getShippingAddress());
        if (empty($matchedZones)) {
            return [];
        }

        $zones = [];
        foreach ($matchedZones as $zone) {
            $zones[] = $zone->getId();
        }

        return $zones;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ShippingSubjectInterface $subject)
    {
        return $subject instanceof ShipmentInterface &&
            null !== $subject->getOrder() &&
            null !== $subject->getOrder()->getShippingAddress()
        ;
    }
}
