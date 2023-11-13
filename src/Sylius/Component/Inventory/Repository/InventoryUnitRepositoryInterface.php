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

namespace Sylius\Component\Inventory\Repository;

use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of InventoryUnitInterface
 *
 * @extends RepositoryInterface<T>
 */
interface InventoryUnitRepositoryInterface extends RepositoryInterface
{
}
