<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Repository;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Shipping\Repository\ShippingMethodRepositoryInterface as BaseShippingMethodRepositoryInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
interface ShippingMethodRepositoryInterface extends BaseShippingMethodRepositoryInterface
{
    /**
     * @param array $zones
     * @param ChannelInterface $channel
     *
     * @return array
     */
    public function findEnabledForZonesAndChannel(array $zones, ChannelInterface $channel);

    /**
     * @param ChannelInterface $channel
     *
     * @return array
     */
    public function findEnabledForChannel(ChannelInterface $channel);
}
