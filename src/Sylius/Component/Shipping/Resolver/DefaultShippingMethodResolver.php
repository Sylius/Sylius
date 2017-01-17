<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Resolver;

use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Repository\ShippingMethodRepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class DefaultShippingMethodResolver implements DefaultShippingMethodResolverInterface
{
    /**
     * @var ShippingMethodRepositoryInterface
     */
    private $shippingMethodRepository;

    /**
     * @param ShippingMethodRepositoryInterface $shippingMethodRepository
     */
    public function __construct(ShippingMethodRepositoryInterface $shippingMethodRepository)
    {
        $this->shippingMethodRepository = $shippingMethodRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultShippingMethod(ShipmentInterface $shipment)
    {
        $shippingMethods = $this->shippingMethodRepository->findBy(['enabled' => true]);
        if (empty($shippingMethods)) {
            throw new UnresolvedDefaultShippingMethodException();
        }
        
        return $shippingMethods[0];
    }
}
