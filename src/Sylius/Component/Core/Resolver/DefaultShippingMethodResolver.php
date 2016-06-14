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
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Repository\ShippingMethodRepositoryInterface;
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
    public function getDefaultShippingMethod(ShipmentInterface $shippingSubject)
    {
        Assert::isInstanceOf($shippingSubject, \Sylius\Component\Core\Model\ShipmentInterface::class);

        $shippingMethods = $this->shippingMethodRepository->findBy(['enabled' => true]);
        if (empty($shippingMethods)) {
            return null;
        }

        /** @var ChannelInterface $channel */
        $channel = $shippingSubject->getOrder()->getChannel();

        foreach ($shippingMethods as $key => $shippingMethod) {
            if (!$channel->hasShippingMethod($shippingMethod)) {
                unset($shippingMethods[$key]);
            }
        }

        if (empty($shippingMethods)) {
            return null;
        }

        return array_values($shippingMethods)[0];
    }
}
