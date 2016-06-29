<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Resolver;

use Sylius\Core\Model\ChannelInterface;
use Sylius\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Shipping\Model\ShipmentInterface;
use Sylius\Core\Model\ShipmentInterface as CoreShipmentInterface;
use Sylius\Shipping\Repository\ShippingMethodRepositoryInterface;
use Sylius\Shipping\Resolver\DefaultShippingMethodResolverInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class DefaultShippingMethodResolver implements DefaultShippingMethodResolverInterface
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
        Assert::isInstanceOf($shipment, CoreShipmentInterface::class);

        $shippingMethods = $this->shippingMethodRepository->findBy(['enabled' => true]);
        if (empty($shippingMethods)) {
            throw new UnresolvedDefaultShippingMethodException();
        }

        /** @var ChannelInterface $channel */
        $channel = $shipment->getOrder()->getChannel();

        foreach ($shippingMethods as $shippingMethod) {
            if ($channel->hasShippingMethod($shippingMethod)) {
                return $shippingMethod;
            }
        }

        throw new UnresolvedDefaultShippingMethodException();
    }
}
