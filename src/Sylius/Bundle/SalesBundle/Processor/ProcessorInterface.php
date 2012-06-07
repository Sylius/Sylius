<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Processor;

use Sylius\Bundle\SalesBundle\Model\OrderInterface;
use Sylius\Bundle\SalesBundle\Processor\Operation\OperationInterface;

/**
 * Interface for order processor.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ProcessorInterface
{
    /**
     * Prepares order for processing.
     *
     * @param OrderInterface $order
     */
    function prepare(OrderInterface $order);

    /**
     * Processes order.
     * This action is fired just before saving the order.
     *
     * @param OrderInterface $order
     */
    function process(OrderInterface $order);

    /**
     * Finalizes order.
     * This action is fired after placing the order.
     *
     * @param OrderInterface $order
     */
    function finalize(OrderInterface $order);

    /**
     * Registers processor.
     *
     * @param string             $alias
     * @param OperationInterface $operation
     */
    function registerOperation($alias, OperationInterface $operation);

    /**
     * Unergisters operation.
     *
     * @param string $alias
     */
    function unregisterOperation($alias);
}
