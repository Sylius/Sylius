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

namespace Sylius\Bundle\InventoryBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Repository\InventoryUnitRepositoryInterface;

/**
 * @template T of InventoryUnitInterface
 *
 * @implements InventoryUnitRepositoryInterface<T>
 */
class InventoryUnitRepository extends EntityRepository implements InventoryUnitRepositoryInterface
{
}
