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
 * Order processor.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Processor implements ProcessorInterface
{
    /**
     * Processor operation.
     *
     * @var array
     */
    private $operations = array();

    /**
     * Prepares order for processing.
     * Calls all operations.
     *
     * @param OrderInterface $order
     */
    public function prepare(OrderInterface $order)
    {
        foreach ($this->operations as $operation) {
            $operation->prepare($order);
        }
    }

    /**
     * Processes order. Calls all operations.
     *
     * @param OrderInterface $order
     */
    public function process(OrderInterface $order)
    {
        foreach ($this->operations as $operation) {
            $operation->process($order);
        }
    }

    /**
     * Finalizes order.
     *
     * @param OrderInterface $order
     */
    public function finalize(OrderInterface $order)
    {
        foreach ($this->operations as $operation) {
            $operation->finalize($order);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function registerOperation($alias, OperationInterface $operation)
    {
        $this->operations[$alias] = $operation;
    }

    /**
     * {@inheritdoc}
     */
    public function unregisterOperation($alias)
    {
        if (!isset($this->operations[$alias])) {
            throw new \InvalidArgumentException(sprintf('Operation with alias "%s" is not registered', $alias));
        }

        unset($this->operations[$alias]);
    }
}
