<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\ShippingMethod\Updater;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Core\ShippingMethod\Updater\ChannelAwareShippingMethodUpdaterInterface;

readonly class ShippingMethodUpdater implements ChannelAwareShippingMethodUpdaterInterface
{
    public function __construct(
        private ShippingMethodRepositoryInterface $shippingMethodRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function removeChannelConfigurationFromShippingMethods(string $channelCode): void
    {
        $shippingMethods = $this->shippingMethodRepository->findByChannelCodeInConfiguration($channelCode);

        foreach ($shippingMethods as $shippingMethod) {
            $configuration = $shippingMethod->getConfiguration();
            unset($configuration[$channelCode]);
            $shippingMethod->setConfiguration($configuration);
        }

        $this->entityManager->flush();
    }
}
