<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Repository;

use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lchrusciel@gmail.com>
 */
interface OrderItemRepositoryInterface extends RepositoryInterface
{
    /**
     * @param mixed $id
     * @param mixed $cartId
     *
     * @return OrderItemInterface
     */
    public function findOneByIdAndCartId($id, $cartId);
}
