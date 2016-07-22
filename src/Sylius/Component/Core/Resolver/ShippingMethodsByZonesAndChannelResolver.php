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
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Resolver\MethodsResolverInterface;
use Webmozart\Assert\Assert;

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
        /** @var ShipmentInterface $subject */
        Assert::true($this->supports($subject));
        /** @var OrderInterface $order */
        $order = $subject->getOrder();

        $zones = $this->zoneMatcher->matchAll($order->getShippingAddress());
        if (empty($zones)) {
            return [];
        }

        return $this->shippingMethodRepository->findEnabledForZonesAndChannel($zones, $order->getChannel());
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ShippingSubjectInterface $subject)
    {
        return $subject instanceof ShipmentInterface &&
            null !== $subject->getOrder() &&
            null !== $subject->getOrder()->getShippingAddress() &&
            null !== $subject->getOrder()->getChannel()
        ;
    }
}
