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

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface as BaseOrderItemRepositoryInterface;

/**
 * @template T of OrderItemInterface
 *
 * @extends BaseOrderItemRepositoryInterface<T>
 */
interface OrderItemRepositoryInterface extends BaseOrderItemRepositoryInterface
{
    public function findOneByIdAndCustomer($id, CustomerInterface $customer): ?OrderItemInterface;
}
