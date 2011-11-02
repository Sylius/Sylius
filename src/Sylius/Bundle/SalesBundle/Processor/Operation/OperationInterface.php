<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Processor\Operation;

use Sylius\Bundle\SalesBundle\Model\OrderInterface;

/**
 * Interface for order processor operations.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface OperationInterface
{
    /**
     * Prepares order for processing.
     *
     * @param OrderInterface $order
     */
    function prepare(OrderInterface $order);
    
    /**
     * Processes order.
     * 
     * @param OrderInterface $order
     */
    function process(OrderInterface $order);
}
