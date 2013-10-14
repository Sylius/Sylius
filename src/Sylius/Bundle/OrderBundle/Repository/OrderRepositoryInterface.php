<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Repository;

use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;

/**
 * Order repository interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface OrderRepositoryInterface extends RepositoryInterface
{
    /**
     * Get recently completed orders.
     *
     * @return array
     */
    public function findRecentOrders($amount = 10);
}
