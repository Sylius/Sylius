<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Builder;

use Sylius\Bundle\SalesBundle\Model\OrderInterface;

/**
 * Order builder interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface OrderBuilderInterface
{
    /**
     * Build order, from a cart for example.
     *
     * @param OrderInterface $order
     */
    public function build(OrderInterface $order);

    /**
     * Finalize order.
     *
     * @param OrderInterface $order
     */
    public function finalize(OrderInterface $order);
}
