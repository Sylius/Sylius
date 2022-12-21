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

namespace Sylius\Component\Shipping\Resolver;

use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Repository\ShippingMethodRepositoryInterface;
use Webmozart\Assert\Assert;

final class DefaultShippingMethodResolver implements DefaultShippingMethodResolverInterface
{
    public function __construct(private ShippingMethodRepositoryInterface $shippingMethodRepository)
    {
    }

    public function getDefaultShippingMethod(ShipmentInterface $shipment): ShippingMethodInterface
    {
        $shippingMethods = $this->shippingMethodRepository->findBy(['enabled' => true]);
        if (empty($shippingMethods)) {
            throw new UnresolvedDefaultShippingMethodException();
        }
        $shippingMethod = $shippingMethods[0];
        Assert::isInstanceOf($shippingMethod, ShippingMethodInterface::class);

        return $shippingMethod;
    }
}
