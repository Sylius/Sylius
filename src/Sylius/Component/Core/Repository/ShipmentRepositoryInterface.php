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
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface ShipmentRepositoryInterface extends RepositoryInterface
{
    public function createListQueryBuilder(): QueryBuilder;

    public function findOneByOrderId($shipmentId, $orderId): ?ShipmentInterface;

    public function findOneByOrderTokenAndChannel($shipmentId, string $tokenValue, ChannelInterface $channel): ?ShipmentInterface;

    public function findOneByCustomer($id, CustomerInterface $customer): ?ShipmentInterface;

    /**
     * @return array|ShipmentInterface[]
     */
    public function findByName(string $name, string $locale): array;
}
