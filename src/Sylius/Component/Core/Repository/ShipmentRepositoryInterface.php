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
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ShipmentRepositoryInterface extends RepositoryInterface
{
    /**
     * @return QueryBuilder
     */
    public function createListQueryBuilder(): QueryBuilder;

    /**
     * @param mixed $shipmentId
     * @param mixed $orderId
     *
     * @return ShipmentInterface|null
     */
    public function findOneByOrderId($shipmentId, $orderId): ?ShipmentInterface;

    /**
     * @param string $name
     * @param string $locale
     *
     * @return array|ShipmentInterface[]
     */
    public function findByName(string $name, string $locale): array;
}
