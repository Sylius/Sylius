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

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShipmentInterface as CoreShipmentInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Checker\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolverInterface;
use Webmozart\Assert\Assert;

final class CategoryBasedDefaultShippingMethodResolver implements DefaultShippingMethodResolverInterface
{
    /**
     * @var ShippingMethodRepositoryInterface
     */
    private $shippingMethodRepository;

    /**
     * @var ShippingMethodEligibilityCheckerInterface
     */
    private $shippingMethodEligibilityChecker;

    /**
     * @param ShippingMethodRepositoryInterface $shippingMethodRepository
     * @param ShippingMethodEligibilityCheckerInterface $shippingMethodEligibilityChecker
     */
    public function __construct(
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodEligibilityCheckerInterface $shippingMethodEligibilityChecker
    ) {
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->shippingMethodEligibilityChecker = $shippingMethodEligibilityChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultShippingMethod(ShipmentInterface $shipment): ShippingMethodInterface
    {
        /** @var CoreShipmentInterface $shipment */
        Assert::isInstanceOf($shipment, CoreShipmentInterface::class);

        /** @var ChannelInterface $channel */
        $channel = $shipment->getOrder()->getChannel();

        $shippingMethods = $this->shippingMethodRepository->findEnabledForChannel($channel);

        foreach ($shippingMethods as $key => $shippingMethod) {
            if ($this->shippingMethodEligibilityChecker->isEligible($shipment, $shippingMethod)) {
                return $shippingMethod;
            }
        }

        throw new UnresolvedDefaultShippingMethodException();
    }
}
