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

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShipmentInterface as CoreShipmentInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolverInterface;
use Webmozart\Assert\Assert;

@trigger_error(sprintf('This class is deprecated since Sylius 1.2 and will be removed in 2.0. "%s" should be used instead.', EligibleDefaultShippingMethodResolver::class), \E_USER_DEPRECATED);

class DefaultShippingMethodResolver implements DefaultShippingMethodResolverInterface
{
    public function __construct(private ShippingMethodRepositoryInterface $shippingMethodRepository)
    {
    }

    public function getDefaultShippingMethod(ShipmentInterface $shipment): ShippingMethodInterface
    {
        /** @var CoreShipmentInterface $shipment */
        Assert::isInstanceOf($shipment, CoreShipmentInterface::class);

        $order = $shipment->getOrder();

        /** @var ChannelInterface $channel */
        $channel = $order->getChannel();

        $shippingMethods = $this->shippingMethodRepository->findEnabledForChannel($channel);
        if (empty($shippingMethods)) {
            throw new UnresolvedDefaultShippingMethodException();
        }

        return $shippingMethods[0];
    }
}
