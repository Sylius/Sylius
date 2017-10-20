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

namespace Sylius\Component\Core\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Repository\ShippingMethodRepositoryInterface as BaseShippingMethodRepositoryInterface;

interface ShippingMethodRepositoryInterface extends BaseShippingMethodRepositoryInterface
{
    /**
     * @param string $locale
     *
     * @return QueryBuilder
     */
    public function createListQueryBuilder(string $locale): QueryBuilder;

    /**
     * @param ChannelInterface $channel
     *
     * @return array|ShippingMethodInterface[]
     */
    public function findEnabledForChannel(ChannelInterface $channel): array;

    /**
     * @param array $zones
     * @param ChannelInterface $channel
     *
     * @return array|ShippingMethodInterface[]
     */
    public function findEnabledForZonesAndChannel(array $zones, ChannelInterface $channel): array;
}
