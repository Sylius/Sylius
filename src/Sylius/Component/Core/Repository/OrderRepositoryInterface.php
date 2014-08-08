<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Repository;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface as BaseOrderRepositoryInterface;

interface OrderRepositoryInterface extends BaseOrderRepositoryInterface
{
    /**
     * Gets expired orders.
     *
     * @param \DateTime $expiresAt
     * @param string    $state
     *
     * @return OrderInterface[]
     */
    public function findExpired(\DateTime $expiresAt, $state = OrderInterface::STATE_PENDING);
}
