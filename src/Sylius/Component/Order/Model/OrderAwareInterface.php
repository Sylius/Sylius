<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Model;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
interface OrderAwareInterface
{
    /**
     * @return OrderInterface
     */
    public function getOrder();

    /**
     * @param OrderInterface $order
     */
    public function setOrder(OrderInterface $order = null);
}
