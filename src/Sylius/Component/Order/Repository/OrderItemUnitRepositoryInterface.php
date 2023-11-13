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

namespace Sylius\Component\Order\Repository;

use Sylius\Component\Order\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of OrderItemUnitInterface
 *
 * @extends RepositoryInterface<T>
 */
interface OrderItemUnitRepositoryInterface extends RepositoryInterface
{
}
