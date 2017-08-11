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

namespace Sylius\Component\Core\Checker;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class OrderShippingMethodSelectionRequirementChecker implements OrderShippingMethodSelectionRequirementCheckerInterface
{
    /**
     * @var ShippingMethodsResolverInterface
     */
    private $shippingMethodsResolver;

    /**
     * @param ShippingMethodsResolverInterface $shippingMethodsResolver
     */
    public function __construct(ShippingMethodsResolverInterface $shippingMethodsResolver)
    {
        $this->shippingMethodsResolver = $shippingMethodsResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function isShippingMethodSelectionRequired(OrderInterface $order)
    {
        if (!$order->isShippingRequired()) {
            return false;
        }

        if (!$order->hasShipments()) {
            return true;
        }

        if (!$order->getChannel()->isSkippingShippingStepAllowed()) {
            return true;
        }

        /** @var ShipmentInterface $shipment */
        foreach ($order->getShipments() as $shipment) {
            if (1 !== count($this->shippingMethodsResolver->getSupportedMethods($shipment))) {
                return true;
            }
        }

        return false;
    }
}
