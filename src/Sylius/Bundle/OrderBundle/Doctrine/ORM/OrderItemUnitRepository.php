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

namespace Sylius\Bundle\OrderBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Order\Model\OrderItemUnitInterface;
use Sylius\Component\Order\Repository\OrderItemUnitRepositoryInterface;

/**
 * @template T of OrderItemUnitInterface
 *
 * @implements OrderItemUnitRepositoryInterface<T>
 */
class OrderItemUnitRepository extends EntityRepository implements OrderItemUnitRepositoryInterface
{
}
