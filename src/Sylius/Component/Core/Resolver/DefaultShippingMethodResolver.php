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
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShipmentInterface as CoreShipmentInterface;
use Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolverInterface;
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
        /** @var CoreShipmentInterface $shipment */
        Assert::isInstanceOf($shipment, CoreShipmentInterface::class);

        /** @var ChannelInterface $channel */
        $channel = $shipment->getOrder()->getChannel();

        $shippingMethods = $this->shippingMethodRepository->findEnabledForChannel($channel);
        if (empty($shippingMethods)) {
            throw new UnresolvedDefaultShippingMethodException();
        }

        return $shippingMethods[0];
    }
}
