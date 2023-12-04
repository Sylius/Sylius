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

namespace Sylius\Component\Core\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Repository\ShippingMethodRepositoryInterface as BaseShippingMethodRepositoryInterface;

/**
 * @template T of ShippingMethodInterface
 *
 * @extends BaseShippingMethodRepositoryInterface<T>
 */
interface ShippingMethodRepositoryInterface extends BaseShippingMethodRepositoryInterface
{
    public function createListQueryBuilder(string $locale): QueryBuilder;

    /**
     * @return array|ShippingMethodInterface[]
     */
    public function findEnabledForChannel(ChannelInterface $channel): array;

    /**
     * @return array|ShippingMethodInterface[]
     */
    public function findEnabledForZonesAndChannel(array $zones, ChannelInterface $channel): array;
}
